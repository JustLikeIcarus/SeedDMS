START TRANSACTION;

ALTER TABLE tblSessions ADD COLUMN `su` INTEGER DEFAULT NULL;

UPDATE tblVersion set major=4, minor=2, subminor=0;

COMMIT;

