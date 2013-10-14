<?php

/*************************************************************************
 * password_strength.class.php                                           *
 *************************************************************************
 *                                                                       *
 * (c) 2008-2011 Wolf Software Limited <support@wolf-software.com>       *
 * All Rights Reserved.                                                  *
 *                                                                       *
 * This program is free software: you can redistribute it and/or modify  *
 * it under the terms of the GNU General Public License as published by  *
 * the Free Software Foundation, either version 3 of the License, or     *
 * (at your option) any later version.                                   *
 *                                                                       *
 * This program is distributed in the hope that it will be useful,       *
 * but WITHOUT ANY WARRANTY; without even the implied warranty of        *
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         *
 * GNU General Public License for more details.                          *
 *                                                                       *
 * You should have received a copy of the GNU General Public License     *
 * along with this program.  If not, see <http://www.gnu.org/licenses/>. *
 *                                                                       *
 *************************************************************************/

class Password_Strength {
	private $class_name      = "password strength";
	private $class_version   = "1.0.0";
	private $class_author    = "wolf software";
	private $class_source    = "http://www.wolf-software.com/downloads/php-classes/security-classes/password-strength-class/";

	private $password        = '';
	private $password_info   = array();
	private $password_length = 0;
	private $score_precision = 2;

	public function class_name() {
		return $this->class_name;
	}

	public function class_version() {
		return $this->class_version;
	}

	public function class_author() {
		return $this->class_author;
	}

	public function class_source() {
		return $this->class_source;
	}

	public function __construct() {
	}

	public function simple_calculate() {
		$password = $this->password;
		$score = 0;
		if(preg_match('/[0-9]+/', $password))
			$score += 25;
		if(preg_match('/[a-z]+/', $password))
			$score += 25;
		if(preg_match('/[A-Z]+/', $password))
			$score += 25;
		if(preg_match('/[^0-9a-zA-Z]+/', $password))
			$score += 25;
		if($this->password_length < 8)
			$score *= ($this->password_length/8);

		$this->password_info['total_score'] = $score;
		$this->password_info['rating_score'] = $score;
		$this->password_info['rating'] = $this->get_score_info($score);
	}

	public function calculate() {
		$this->password_info = array();
		
		$this->password_info['password'] = $this->password;
		$this->password_info['password_length'] = $this->password_length;
		if($this->password_length == 0) {
			$this->password_info['total_score'] = 0;
			$this->password_info['rating_score'] = 0;
			$this->password_info['rating'] = 'Very Bad';
			return;
		}

		$this->calculate_length();
		$this->calculate_complexity();
		$this->calculate_charset_complexity();
		$this->calculate_entropy();

		$total = 0;
		$scoreCount = 0;
		$keys = array_keys($this->password_info['details']);
		foreach ($keys as $key) {
			if (preg_match('/score+$/', $key)) {
				$total += intval($this->password_info['details'][$key]);
				$scoreCount ++;
			}
		}
		$rating_score = round($total / $scoreCount, $this->score_precision);
		$score_info = $this->get_score_info($rating_score);

		$this->password_info['total_score'] = $total;
		$this->password_info['rating_score'] = $rating_score;
		$this->password_info['rating'] = $score_info;

		ksort($this->password_info);
		ksort($this->password_info['details']);
	}

	public function get_all_info() {
		return $this->password_info;
	}

	public function get_score() {
		return $this->password_info['rating_score'];
	}

	public function get_rating() {
		return $this->password_info['rating'];
	}

	public function set_password($password) {
		$this->password = $password;
		$this->password_length = strlen($password);
	}

	private function calculate_charset_complexity() {
		$password = $this->password;
		$len = strlen($password);

		$char = '';
		$last_char = '';
		$different_count = 0;
		$score = 0;

		if ($len <= 3) {
			$score = 2;
		} else {
			for ($i = 0; $i < $len; $i++) {
				$char = substr($password, $i, 1);
				if ($i > 0) {
					$last_char = substr($password, $i - 1, 1);
				}
				if ($char != $last_char) {
					$different_count++;
				}
			}
			if ($len <= 5) {
				$score = 10;
			} elseif ($different_count == 1) {
				$score = 1;
				$this->password_info['details']['length_score'] = min(min(floor(10 * $this->password_length / 10), 20), $this->password_info['details']['length_score']);
			} elseif ($different_count == 2) {
				$score = 5;
				$this->password_info['details']['length_score'] = min(min(floor(20 * $this->password_length / 10), 40), $this->password_info['details']['length_score']);
			} elseif ($different_count == 3) {
				$score = 10;
						$this->password_info['details']['length_score'] = min(min(floor(30 * $this->password_length / 10), 50), $this->password_info['details']['length_score']);
			} else {
				$score = round(max($this->password_info['details']['length_score'] / 10, $different_count / $len * 100), $this->score_precision);
			}
		}
		$this->password_info['details']['charset_complexity_score'] = $score;
	}

  private function calculate_complexity() {
		$password = $this->password;
		$score = 0;

		if (preg_match('/^([0-9]+)+$/', $password)) {
			$score = 10;
			$this->password_info['details']['charset'] = 'numeric';
		} elseif (preg_match('/^([a-z]+)+$/', $password)) {
			$score = 30;
			$this->password_info['details']['charset'] = 'alphabetic';
		} elseif (preg_match('/^([a-z0-9]+)+$/i', $password)) {
			if ((preg_match('/^([a-z]+)([0-9]+)+$/i', $password, $match)) || (preg_match('/^([0-9]+)([a-z]+)+$/i', $password, $match))) {
				$alpha = $match[1];
				$numeric = $match[2];
				$numeric_length = strlen($numeric);

				if (($numeric == 111) || ($numeric == 123)) {
					if (preg_match('/^([a-z]+)([0-9]+)+$/i', $password, $match)) {
						$score = 31;
					} else {
						$score = 35;
					}
					$this->password_info['details']['common_numeric'] = true;
				} elseif ($numeric_length == 1) {
					$score = 30;
				} elseif ($numeric_length <= 3) {
					$score = 35;
				} elseif ($numeric_length <= 5) {
					$score = 40;
				} elseif ($numeric_length <= 10) {
					$score = 50;
				} else {
					$score = 60;
				}
				$this->password_info['details']['charset'] = 'alphanumeric';
			} else {
				$score = 80;
				$this->password_info['details']['charset'] = 'alphanumeric';
			}
		} else {
			$score = 100;
			$this->password_info['details']['charset'] = 'alphanumeric + others';
		}
		$this->password_info['details']['charset_score'] = $score;
	}

  private function calculate_length() {
		$len = $this->password_length;
		$score = 0;

		if ($len == 0) {
			$score = 0;
		} elseif ($len <= 3) {
			$score = 1;
		} elseif ($len <= 4) {
			$score = 2;
		} elseif ($len <= 5) {
			$score = 10;
		} elseif ($len <= 6) {
			$score = 20;
		} elseif ($len <= 8) {
			$score = 30;
		} elseif ($len <= 10) {
			$score = 45;
		} elseif ($len <= 15) {
			$score = 75;
		} elseif ($len <= 18) {
			$score = 80;
		} elseif ($len <= 20) {
			$score = 90;
		} else {
			$score = 100;
		}
		$this->password_info['details']['length_score'] = $score;
	}

	private function calculate_entropy() {
		$score = 0;
		$password = $this->password;
		$length = $this->password_length;

		foreach (count_chars($password, 1) as $v) {
			$p = $v / $length;
			$score -= $p * log($p)/log(2);
		}
		$this->password_info['details']['entropy_per_character'] = round($score, $this->score_precision);
		$this->password_info['details']['entropy_score'] = round(($score * $length), $this->score_precision);
	}

	private function get_score_info($score) {
		if ($score <= 15) {
			$score_info = 'Very Bad';
		} elseif ($score <= 35) {
			$score_info = 'Bad';
		} elseif ($score <= 45) {
			$score_info = 'Medium - Bad';
		} elseif ($score <= 55) {
			$score_info = 'Medium';
		} elseif ($score <= 65) {
			$score_info = 'Medium - Good';
		} elseif ($score <= 75) {
			$score_info = 'Good';
		} elseif ($score <= 90) {
			$score_info = 'Very Good';
		} else {
			$score_info = 'Excellent';
		}
		return $score_info;
	}
}
?>
