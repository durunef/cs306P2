DELIMITER $$

CREATE TRIGGER trg_prevent_late_cancellation
BEFORE DELETE ON Attendance
FOR EACH ROW
BEGIN
    DECLARE class_datetime DATETIME;
    DECLARE current_time DATETIME;

    -- Get full class datetime
    SELECT CONCAT(OLD.Date, ' ', c.Schedule) INTO class_datetime
    FROM Class c
    WHERE c.Class_ID = OLD.Class_ID;

    -- Get current timestamp
    SET current_time = NOW();

    -- Compare: if class is less than 12 hours away, prevent deletion
    IF TIMESTAMPDIFF(HOUR, current_time, class_datetime) < 12 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Cancellation denied: Cannot cancel less than 12 hours before class.';
    END IF;
END$$

DELIMITER ;
