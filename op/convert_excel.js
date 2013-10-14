//    MyDMS. Document Management System
//    Copyright (C) 2002-2005  Markus Westphal
//    Copyright (C) 2006-2008 Malcolm Cowe
//
//    This program is free software; you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation; either version 2 of the License, or
//    (at your option) any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program; if not, write to the Free Software
//    Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.

//var logFile = fs.CreateTextFile("convertlog.txt", true);

var source = WScript.Arguments(0);
var target = WScript.Arguments(1);

var ExcelApp;
ExcelApp = new ActiveXObject("Excel.Application");
var Newdoc;
Newdoc = ExcelApp.Workbooks.Open(source);
Newdoc.SaveAs(target, 44); // xlHTML = 44
ExcelApp.Quit();



//logFile.Close();
