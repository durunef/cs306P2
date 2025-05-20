DROP TABLE IF EXISTS Attendance;
DROP TABLE IF EXISTS Payment;
DROP TABLE IF EXISTS Class;
DROP TABLE IF EXISTS Trainer;
DROP TABLE IF EXISTS Member;
DROP TABLE IF EXISTS Membership_Plan;

-- 📦 Create the database
CREATE DATABASE IF NOT EXISTS GymDB;
USE GymDB;

-- 📌 Membership Plan Table
CREATE TABLE Membership_Plan (
    Plan_ID INT PRIMARY KEY,
    Plan_Name VARCHAR(100),
    Duration INT, -- in months
    Cost DECIMAL(10,2)
);

-- 🧍 Member Table
CREATE TABLE Member (
    Member_ID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(100),
    Age INT,
    Gender VARCHAR(10),
    Contact_Info VARCHAR(255),
    Plan_ID INT,
    FOREIGN KEY (Plan_ID) REFERENCES Membership_Plan(Plan_ID) ON DELETE CASCADE
);


-- 🧑‍🏫 Trainer Table
CREATE TABLE Trainer (
    Trainer_ID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(100),
    Specialization VARCHAR(100),
    Contact_Info VARCHAR(255),
    Salary DECIMAL(10,2)
);

-- 🏋️ Class Table
CREATE TABLE Class (
    Class_ID INT AUTO_INCREMENT PRIMARY KEY,
    Class_Name VARCHAR(100),
    Trainer_ID INT,
    Schedule TIME,
    Capacity INT,
    FOREIGN KEY (Trainer_ID) REFERENCES Trainer(Trainer_ID) ON DELETE SET NULL
);

-- 💳 Payment Table
CREATE TABLE Payment (
    Payment_ID INT AUTO_INCREMENT PRIMARY KEY,
    Member_ID INT,
    Amount DECIMAL(10,2),
    Date DATE,
    Payment_Method VARCHAR(50),
    FOREIGN KEY (Member_ID) REFERENCES Member(Member_ID) ON DELETE CASCADE
);

-- 📅 Attendance Table
CREATE TABLE Attendance (
    Attendance_ID INT AUTO_INCREMENT PRIMARY KEY,
    Member_ID INT,
    Class_ID INT,
    Date DATE,
    Status VARCHAR(50),
    FOREIGN KEY (Member_ID) REFERENCES Member(Member_ID) ON DELETE CASCADE,
    FOREIGN KEY (Class_ID) REFERENCES Class(Class_ID) ON DELETE CASCADE
);

-- 🔽 Insert Membership Plans
INSERT INTO Membership_Plan (Plan_ID, Plan_Name, Duration, Cost) VALUES
(1, 'Basic', 1, 500.00),
(2, 'Standard', 3, 1400.00),
(3, 'Premium', 6, 2700.00),
(4, 'Gold', 12, 5200.00),
(5, 'Platinum', 24, 9700.00),
(6, 'Student Discount', 6, 1800.00),
(7, 'Couples Plan', 12, 4000.00),
(8, 'Family Plan', 12, 6800.00),
(9, 'Retirement Plan', 6, 1600.00),
(10, 'Corporate Plan', 12, 8500.00);

-- 👤 Insert Members
INSERT INTO Member (Member_ID, Name, Age, Gender, Contact_Info, Plan_ID) VALUES
(101, 'Zeynep Dagci', 28, 'Female', 'zeynep.dagci@sabanciuniv.edu', 2),
(102, 'Duru Nef Ozmen', 35, 'Male', 'duru.ozmen@sabanciuniv.edu', 1),
(103, 'Omer Fatih Tarim', 22, 'Male', 'omer.tarim@sabanciuniv.edu', 3),
(104, 'Duygu Tosun', 40, 'Female', 'duygu.tosun@sabanciuniv.edu', 4),
(105, 'Bora Ilan', 29, 'Male', 'bora.ilan@sabanciuniv.edu', 5),
(106, 'Dilara Yildiz', 31, 'Female', 'dilara.yildiz@sabanciuniv.edu', 6),
(107, 'Turker Dagci', 50, 'Male', 'turker.dagci@sabanciuniv.edu', 7),
(108, 'Bennu Ozmen', 24, 'Female', 'bennu.ozmen@sabanciuniv.edu', 8),
(109, 'Cengizhan Kinay', 55, 'Male', 'cengizhan.kinay@sabanciuniv.edu', 9),
(110, 'Ceren Oztekin', 33, 'Female', 'ceren.oztekin@sabanciuniv.edu', 10);

-- 🧑‍🏫 Insert Trainers
INSERT INTO Trainer (Trainer_ID, Name, Specialization, Contact_Info, Salary) VALUES
(201, 'Mehmet Yilmaz', 'Yoga', 'mehmet.yilmaz@gmail.com', 65000.00),
(202, 'Elif Demir', 'Weight Training', 'elif.demir@gmail.com', 72000.00),
(203, 'Ahmet Kaya', 'Cardio', 'ahmet.kaya@gmail.com', 70000.00),
(204, 'Zeynep Aksoy', 'Pilates', 'zeynep.aksoy@gmail.com', 67000.00),
(205, 'Burak Sahin', 'CrossFit', 'burak.sahin@gmail.com', 78000.00),
(206, 'Selin Ozturk', 'Zumba', 'selin.ozturk@gmail.com', 68000.00),
(207, 'Can Taner', 'Strength Training', 'can.taner@gmail.com', 75000.00),
(208, 'Deniz Aydin', 'Spinning', 'deniz.aydin@gmail.com', 71000.00),
(209, 'Emre Karaca', 'Martial Arts', 'emre.karaca@gmail.com', 79000.00),
(210, 'Buse Koc', 'Aerobics', 'buse.koc@gmail.com', 66000.00);

-- 🕒 Insert Classes
INSERT INTO Class (Class_ID, Class_Name, Trainer_ID, Schedule, Capacity) VALUES
(301, 'Morning Yoga', 201, '08:00:00', 20),
(302, 'Intense Workout', 202, '10:30:00', 25),
(303, 'Evening Cardio', 203, '18:00:00', 30),
(304, 'Pilates Session', 204, '09:00:00', 18),
(305, 'CrossFit Challenge', 205, '11:00:00', 22),
(306, 'Zumba Dance', 206, '16:00:00', 28),
(307, 'Strength Training', 207, '14:00:00', 24),
(308, 'Spinning Class', 208, '07:30:00', 20),
(309, 'Beginner Martial Arts', 209, '17:00:00', 15),
(310, 'Aerobic Blast', 210, '13:00:00', 26);

-- 💰 Insert Payments
INSERT INTO Payment (Payment_ID, Member_ID, Amount, Date, Payment_Method) VALUES
(401, 101, 1400.00, '2025-03-01', 'Credit Card'),
(402, 102, 500.00, '2025-02-15', 'PayPal'),
(403, 103, 2700.00, '2025-01-20', 'Debit Card'),
(404, 104, 5200.00, '2025-03-05', 'Credit Card'),
(405, 105, 9700.00, '2025-03-07', 'Bank Transfer'),
(406, 106, 1800.00, '2025-02-28', 'PayPal'),
(407, 107, 4000.00, '2025-01-15', 'Credit Card'),
(408, 108, 6800.00, '2025-02-10', 'Debit Card'),
(409, 109, 1600.00, '2025-03-12', 'Bank Transfer'),
(410, 110, 8500.00, '2025-03-18', 'PayPal');

-- 📅 Insert Attendance Records
INSERT INTO Attendance (Member_ID, Class_ID, Date, Status) VALUES
(101, 301, '2025-03-01', 'Attended'),
(102, 302, '2025-03-01', 'Missed'),
(103, 303, '2025-03-02', 'Attended'),
(104, 304, '2025-03-02', 'Attended'),
(105, 305, '2025-03-03', 'Missed'),
(106, 306, '2025-03-03', 'Attended'),
(107, 307, '2025-03-04', 'Missed'),
(108, 308, '2025-03-04', 'Attended'),
(109, 309, '2025-03-05', 'Attended'),
(110, 310, '2025-03-06', 'Missed');