DROP TRIGGER IF EXISTS trg_prevent_overbooking;
DELIMITER $$

CREATE TRIGGER trg_prevent_overbooking
BEFORE INSERT ON Attendance
FOR EACH ROW
BEGIN
    DECLARE current_count INT;
    DECLARE class_capacity INT;

    -- Only enforce if the new status is 'Attended'
    IF NEW.Status = 'Attended' THEN
        -- Count how many 'Attended' records already exist
        SELECT COUNT(*) INTO current_count
        FROM Attendance
        WHERE Class_ID = NEW.Class_ID AND Date = NEW.Date AND Status = 'Attended';

        -- Get the class capacity
        SELECT Capacity INTO class_capacity
        FROM Class
        WHERE Class_ID = NEW.Class_ID;

        -- If the class is full, block the insert
        IF current_count >= class_capacity THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Cannot add attendance: Class is already full.';
        END IF;
    END IF;
END$$

DELIMITER ;

-- 🌟 STEP 0: Setup - Temporarily set class capacity to 2 for testing
UPDATE Class
SET Capacity = 2
WHERE Class_ID = 301;

-- ✅ Check class details
SELECT Class_ID, Class_Name, Capacity
FROM Class
WHERE Class_ID = 301;


-- 🌟 STEP 1: Cleanup - Remove any previous test attendance data
DELETE FROM Attendance
WHERE Class_ID = 301 AND Date = '2025-04-10';

-- ✅ Confirm cleanup
SELECT COUNT(*) AS attendees_after_cleanup
FROM Attendance
WHERE Class_ID = 301 AND Date = '2025-04-10' AND Status = 'Attended';


-- 🌟 STEP 2: Insert 2 valid 'Attended' records (these should succeed)
INSERT INTO Attendance (Member_ID, Class_ID, Date, Status)
VALUES 
(101, 301, '2025-04-10', 'Attended'),
(102, 301, '2025-04-10', 'Attended');

INSERT INTO Attendance (Member_ID, Class_ID, Date, Status)
VALUES (104, 301, '2025-04-10', 'Missed');

-- ✅ Check count after valid inserts
SELECT COUNT(*) AS attendees_after_2_inserts
FROM Attendance
WHERE Class_ID = 301 AND Date = '2025-04-10' AND Status = 'Attended';

-- ✅ View current attendance records
SELECT *
FROM Attendance
WHERE Class_ID = 301 AND Date = '2025-04-10';


-- 🌟 STEP 3: Attempt to insert a 3rd 'Attended' record (should fail due to trigger)
-- 🛑 This should be blocked by trg_prevent_overbooking
INSERT INTO Attendance (Member_ID, Class_ID, Date, Status)
VALUES (103, 301, '2025-04-10', 'Attended');


-- 🌟 STEP 4: Insert a 'Missed' record (should succeed)
-- ✅ This should NOT be blocked since it doesn't count against capacity
INSERT INTO Attendance (Member_ID, Class_ID, Date, Status)
VALUES (104, 301, '2025-04-10', 'Missed');


-- 🌟 STEP 5: Final State Check

-- ✅ View all attendance for this class and date
SELECT *
FROM Attendance
WHERE Class_ID = 301 AND Date = '2025-04-10';

-- ✅ Final count of 'Attended' records (should still be 2)
SELECT COUNT(*) AS final_attended_count
FROM Attendance
WHERE Class_ID = 301 AND Date = '2025-04-10' AND Status = 'Attended';
