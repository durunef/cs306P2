-- Register a New Member --

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
    'Mert Can',
    26,
    'Male',
    'mert.can@example.com',
    2
);
SELECT * FROM Member ORDER BY Member_ID DESC LIMIT 5;
