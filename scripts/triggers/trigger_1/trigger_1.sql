-- Prevent Overbooking -- 
-- This trigger ensures that gym classes don't get overbooked
-- It checks the number of attendees before allowing new attendance records
-- If a class is already at full capacity, it prevents additional 'Attended' status entries
-- 'Missed' status entries are not counted for the capacity limit

DROP TRIGGER IF EXISTS trg_prevent_overbooking;
DELIMITER $$

CREATE TRIGGER trg_prevent_overbooking
BEFORE INSERT ON Attendance
FOR EACH ROW
BEGIN
    DECLARE current_count INT;
    DECLARE class_capacity INT;

    IF NEW.Status = 'Attended' THEN
        SELECT COUNT(*) INTO current_count
        FROM Attendance
        WHERE Class_ID = NEW.Class_ID AND Date = NEW.Date AND Status = 'Attended';

        SELECT Capacity INTO class_capacity
        FROM Class
        WHERE Class_ID = NEW.Class_ID;

        IF current_count >= class_capacity THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Cannot add attendance: Class is already full.';
        END IF;
    END IF;
END$$

DELIMITER ;

-- TESTING PART
-- We'll test with a class that has a capacity of 3
-- First, we'll add 3 attendees (should succeed)
-- Then add some 'Missed' entries (should succeed)
-- Finally, try to add a 4th attendee (should fail)

DELETE FROM Attendance WHERE Class_ID = 301 AND Date = '2025-04-10'; --this is to clean up any previous test data
UPDATE Class SET Capacity = 3 WHERE Class_ID = 301;

INSERT INTO Attendance (Member_ID, Class_ID, Date, Status)
VALUES (101, 301, '2025-04-10', 'Attended'); --SUCCESS

INSERT INTO Attendance (Member_ID, Class_ID, Date, Status)
VALUES (102, 301, '2025-04-10', 'Attended'); --SUCCESS

INSERT INTO Attendance (Member_ID, Class_ID, Date, Status)
VALUES (103, 301, '2025-04-10', 'Attended'); --SUCCESS

INSERT INTO Attendance (Member_ID, Class_ID, Date, Status)
VALUES (105, 301, '2025-04-10', 'Missed'); --SUCCESS

INSERT INTO Attendance (Member_ID, Class_ID, Date, Status)
VALUES (106, 301, '2025-04-10', 'Missed'); --SUCCESS

INSERT INTO Attendance (Member_ID, Class_ID, Date, Status)
VALUES (104, 301, '2025-04-10', 'Attended'); --FAILED WILL TRIGGER AN ERROR

SELECT * FROM Attendance
WHERE Class_ID = 301 AND Date = '2025-04-10'; --this is to check if the attendance is recorded
