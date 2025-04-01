-- Update Membership Plan (Upgrade/Downgrade) --
DELIMITER $$
CREATE PROCEDURE sp_update_membership_plan (
    IN p_member_id INT,
    IN p_new_plan_id INT
)
BEGIN
    UPDATE Member
       SET Plan_ID = p_new_plan_id
     WHERE Member_ID = p_member_id;

    -- Optional: Reset or extend membership_expiry
    UPDATE Member AS m
    JOIN Membership_Plan AS p ON m.Plan_ID = p.Plan_ID
       SET m.membership_expiry = DATE_ADD(CURDATE(), INTERVAL p.Duration MONTH)
     WHERE m.Member_ID = p_member_id;
    
    SELECT 'Membership plan updated successfully' AS message;
END$$
DELIMITER ;
