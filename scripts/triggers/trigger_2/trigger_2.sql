DROP TRIGGER IF EXISTS trg_verify_payment_amount;
DELIMITER $$

CREATE TRIGGER trg_verify_payment_amount
BEFORE INSERT ON Payment
FOR EACH ROW
BEGIN
    DECLARE plan_cost DECIMAL(10,2);
    
    -- Get the cost of the plan based on the Plan_ID of the member
    SELECT Cost INTO plan_cost
    FROM Membership_Plan
    WHERE Plan_ID = (SELECT Plan_ID FROM Member WHERE Member_ID = NEW.Member_ID);

    -- Compare the payment amount with the plan's cost
    IF NEW.Amount != plan_cost THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Payment amount does not match the cost of the selected plan.';
    END IF;
END$$

DELIMITER ;
-- =====================================
-- 🧪 TEST: Insert Payment Records
-- =====================================

-- Step 1: Before Trigger Test – View Payment table
-- Checking the Payment table before inserting any records
-- Step 3: Insert Correct Payment Record (will succeed)
-- This should succeed as the amount (1400.00) matches the plan cost for Member_ID 101 (Plan_ID = 2, cost = 1400.00)
INSERT INTO Payment (Member_ID, Amount, Date, Payment_Method)
VALUES (101, 1400.00, '2025-03-01', 'Credit Card');

-- Step 2: Insert Incorrect Payment Record (will trigger error)
-- This should raise an error as the amount (1000.00) does not match the actual cost (1400.00 for plan 2)
INSERT INTO Payment (Member_ID, Amount, Date, Payment_Method)
VALUES (101, 1000.00, '2025-03-01', 'Credit Card');

-- Expected Output: 
-- ERROR 1644 (45000): Payment amount does not match the cost of the selected plan.



-- Step 4: After Trigger Test – View Payment table again
-- Checking the Payment table after the correct insert (should have a new row now)
SELECT * FROM Payment;

-- =====================================
-- Explanation:
-- 1. The trigger `trg_verify_payment_amount` ensures that whenever a new payment is inserted,
--    it checks if the `Amount` matches the cost of the selected plan for the member.
-- 2. The first insert (with incorrect payment) will fail because the amount (1000.00) doesn't match the plan's cost (1400.00).
-- 3. The second insert (with correct payment) will succeed as the payment amount matches the plan's cost.
-- 4. After the test, w
