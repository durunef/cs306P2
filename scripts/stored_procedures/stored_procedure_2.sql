-- Update the payment --

-- This stored procedure handles payment processing for gym members
-- It records payment details (member ID, amount, date, payment method) in the Payment table
-- Returns the payment ID and confirmation message after successful insertion

DROP PROCEDURE IF EXISTS sp_add_payment;
DELIMITER $$

CREATE PROCEDURE sp_add_payment (
    IN p_member_id INT,
    IN p_amount DECIMAL(10,2),
    IN p_date DATE,
    IN p_method VARCHAR(50)
)
BEGIN
    INSERT INTO Payment (
        Member_ID, Amount, Date, Payment_Method
    ) VALUES (
        p_member_id, p_amount, p_date, p_method
    );

    SELECT LAST_INSERT_ID() AS payment_id, 'Payment recorded successfully' AS message;
END$$

DELIMITER ;

CALL sp_add_payment(
    103, -- Member_ID
    2700.00, -- Amount
    '2025-04-09', -- Date
    'Credit Card' -- Payment Method
);
SELECT * FROM Payment ORDER BY Payment_ID DESC LIMIT 5; --this is to check if the payment is recorded
