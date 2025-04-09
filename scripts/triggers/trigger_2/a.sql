-- Check Class Capacity Before Attendance Insert --

DELIMITER $$
CREATE TRIGGER trg_before_attendance_check_capacity
BEFORE INSERT ON Attendance
FOR EACH ROW
BEGIN
    DECLARE current_capacity INT DEFAULT 0;
    DECLARE enrolled_members INT DEFAULT 0;

    SELECT Capacity
      INTO current_capacity
      FROM Class
     WHERE Class_ID = NEW.Class_ID;

    SELECT COUNT(*)
      INTO enrolled_members
      FROM Attendance
     WHERE Class_ID = NEW.Class_ID;

    IF enrolled_members >= current_capacity THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Capacity reached! Cannot add more members to this class.';
    END IF;
END$$
DELIMITER ;
