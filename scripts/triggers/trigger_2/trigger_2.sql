-- Prevent Payment Errors --
-- This trigger ensures that members pay the correct amount for their membership plan
-- It  checks if the payment amount matches the plan's cost
-- If the payment amount doesn't match the plan cost, it prevents the payment from being recorded

DROP TRIGGER IF EXISTS trg_verify_payment_amount;
DELIMITER $$

CREATE TRIGGER trg_verify_payment_amount
BEFORE INSERT ON Payment
FOR EACH ROW
BEGIN
    DECLARE plan_cost DECIMAL(10,2);
    
    SELECT Cost INTO plan_cost
    FROM Membership_Plan
    WHERE Plan_ID = (SELECT Plan_ID FROM Member WHERE Member_ID = NEW.Member_ID);

    IF NEW.Amount != plan_cost THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Payment amount does not match the cost of the selected plan.';
    END IF;
END$$

DELIMITER ;

-- TESTING PART
-- Test with a member who has a plan costing 1400.00
-- First, try to make a payment of 1000.00 (should fail)
-- Then,make the correct payment of 1400.00 (should succeed)

INSERT INTO Payment (Member_ID, Amount, Date, Payment_Method)
VALUES (101, 1400.00, '2025-03-01', 'Credit Card'); --SUCCESS

INSERT INTO Payment (Member_ID, Amount, Date, Payment_Method)
VALUES (101, 1000.00, '2025-03-01', 'Credit Card'); --FAILED WILL TRIGGER AN ERROR

SELECT * FROM Payment; --this is to check if the payment is recorded
