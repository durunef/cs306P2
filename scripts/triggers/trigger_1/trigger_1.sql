-- =====================================
-- ✅ Trigger: Prevent Overbooking
-- =====================================
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

-- =====================================
-- 🧪 TEST: Insert Attendance Records
-- =====================================

-- ✅ Cleanup previous test data
DELETE FROM Attendance WHERE Class_ID = 301 AND Date = '2025-04-10';

-- ✅ Ensure test class has capacity = 3
UPDATE Class SET Capacity = 3 WHERE Class_ID = 301;

-- ✅ First 3 should succeed
INSERT INTO Attendance (Member_ID, Class_ID, Date, Status)
VALUES (101, 301, '2025-04-10', 'Attended');

INSERT INTO Attendance (Member_ID, Class_ID, Date, Status)
VALUES (102, 301, '2025-04-10', 'Attended');

INSERT INTO Attendance (Member_ID, Class_ID, Date, Status)
VALUES (103, 301, '2025-04-10', 'Attended');

-- ✅ These should still SUCCEED (Missed does not count toward limit)
INSERT INTO Attendance (Member_ID, Class_ID, Date, Status)
VALUES (105, 301, '2025-04-10', 'Missed');

INSERT INTO Attendance (Member_ID, Class_ID, Date, Status)
VALUES (106, 301, '2025-04-10', 'Missed');

-- ❌ This one should FAIL (4th Attended)
-- Will trigger: Cannot add attendance: Class is already full.
INSERT INTO Attendance (Member_ID, Class_ID, Date, Status)
VALUES (104, 301, '2025-04-10', 'Attended');


-- =====================================
-- 📊 RESULT: Final Attendance Table View
-- =====================================
SELECT * FROM Attendance
WHERE Class_ID = 301 AND Date = '2025-04-10';
