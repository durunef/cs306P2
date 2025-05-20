-- Register a New Member --

-- This stored procedure handles new member registration in the gym management system
-- It takes member details (name, age, gender, contact info, membership plan) and inserts them into the Member table
-- Returns the newly created member ID

DELIMITER $$

CREATE PROCEDURE sp_register_member (
    IN p_name VARCHAR(100),
    IN p_age INT,
    IN p_gender VARCHAR(10),
    IN p_contact_info VARCHAR(255),
    IN p_plan_id INT
)
BEGIN
    INSERT INTO Member (
        Name, Age, Gender, Contact_Info, Plan_ID
    ) VALUES (
        p_name, p_age, p_gender, p_contact_info, p_plan_id
    );

    SELECT LAST_INSERT_ID() AS new_member_id;
END$$

DELIMITER ;


CALL sp_register_member(
    'Mert Can', --name
    26, --age
    'Male', --gender
    'mert.can@sabanciuniv.edu', --contact info
    2 --plan id
);
SELECT * FROM Member ORDER BY Member_ID DESC LIMIT 5; --this is to check if the member is registered
