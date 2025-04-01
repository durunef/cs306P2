-- Auto-Update Membership Expiry After Payment --

DELIMITER $$
CREATE TRIGGER trg_after_payment_update_expiry
AFTER INSERT ON Payment
FOR EACH ROW
BEGIN
    -- Join Membership_Plan to get the plan duration in months
    UPDATE Member AS m
    JOIN Membership_Plan AS p ON m.Plan_ID = p.Plan_ID
    SET m.membership_expiry = DATE_ADD(NEW.Date, INTERVAL p.Duration MONTH)
    WHERE m.Member_ID = NEW.Member_ID;
END$$
DELIMITER ;
