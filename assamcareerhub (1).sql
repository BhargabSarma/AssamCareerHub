-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 13, 2025 at 08:38 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `assamcareerhub`
--

--
-- Dumping data for table `batches`
--

INSERT INTO `batches` (`batch_id`, `course_id`, `batch_name`, `start_date`, `end_date`, `active`, `created_at`) VALUES
(1, 1, 'VE Batch1', '2025-01-01', '2025-04-01', 1, '2025-01-28 14:54:35'),
(2, 3, 'GD Batch1', '2025-01-01', '2025-03-31', 1, '2025-01-28 15:03:51'),
(3, 4, 'DM batch1', '2025-03-01', '2025-07-31', 1, '2025-02-12 14:01:44'),
(4, 3, 'GD batch2', '2025-03-01', '2025-04-01', 1, '2025-02-12 15:15:41');

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`course_id`, `course_name`, `description`, `duration`, `fee`, `created_by`, `created_at`, `active`, `booking_amount`) VALUES
(1, 'Video Editing course', 'Advanced Course on Video Editing', '3 Months', 17000.00, NULL, '2025-01-28 13:57:20', 1, 1000.00),
(3, 'Graphics Designing', 'Advance Course on graphics designing', '3 months', 18000.00, NULL, '2025-01-28 13:59:09', 1, 1000.00),
(4, 'Digital Marketing', 'Advanced course on digital marketing', '6 months', 20000.00, NULL, '2025-02-12 14:00:57', 1, 2000.00);

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `student_id`, `course_id`, `batch_id`, `booking_amount`, `installment_1`, `installment_2`, `full_payment`, `status`, `payment_date`, `payment_type`, `amount`) VALUES
(1, 31, 3, 2, 1000.00, 8500.00, 8500.00, 0.00, 'Partially Paid', '2025-02-11 14:04:05', '', 1000.00),
(2, 32, 1, 1, 1000.00, 8000.00, 8000.00, 0.00, 'Partially Paid', '2025-02-12 06:34:53', '', 1000.00),
(3, 33, 3, 2, 1000.00, 8500.00, 8500.00, 0.00, 'Partially Paid', '2025-02-12 06:39:40', '', 1000.00),
(4, 34, 3, 2, 1000.00, 8500.00, 8500.00, 0.00, 'Paid', '2025-02-12 07:05:21', '', 9500.00),
(6, 36, 1, 1, 1000.00, 16000.00, 0.00, 16000.00, 'Paid', '2025-02-12 07:39:28', '', 17000.00),
(7, 37, 1, 1, 1000.00, 8000.00, 8000.00, 0.00, 'Paid', '2025-02-12 07:42:42', '', 9000.00),
(8, 38, 1, 1, 1000.00, 16000.00, 0.00, 16000.00, 'Partially Paid', '2025-02-12 07:57:20', 'Online', 17000.00),
(9, 39, 1, 1, 1000.00, 8000.00, 8000.00, 0.00, 'Paid', '2025-02-12 07:58:55', '', 9000.00),
(10, 40, 3, 2, 1000.00, 17000.00, 0.00, 17000.00, 'Partially Paid', '2025-02-12 07:59:53', 'Online', 18000.00),
(11, 41, 3, 2, 1000.00, 17000.00, 0.00, 17000.00, 'Partially Paid', '2025-02-12 08:03:36', 'Bank Transfer', 18000.00),
(12, 42, 1, 1, 1000.00, 16000.00, 0.00, 16000.00, 'Partially Paid', '2025-02-12 08:08:48', 'Online', 17000.00),
(13, 43, 3, 2, 1000.00, 8500.00, 8500.00, 0.00, 'Paid', '2025-02-12 08:09:55', '', 9500.00),
(14, 44, 1, 1, 1000.00, 16000.00, 0.00, 16000.00, 'Partially Paid', '2025-02-12 08:12:16', 'Online', 17000.00),
(15, 45, 1, 1, 1000.00, 0.00, 0.00, 16000.00, 'Paid', '2025-02-12 08:13:58', 'Online', 17000.00),
(16, 46, 1, 1, 1000.00, 0.00, 0.00, 16000.00, 'Paid', '2025-02-12 08:21:42', '', 17000.00),
(17, 47, 1, 1, 1000.00, 16000.00, 0.00, 0.00, 'Partially Paid', '2025-02-12 08:22:13', 'Online', 1000.00),
(18, 48, 3, 2, 1000.00, 8500.00, 8500.00, 0.00, 'Paid', '2025-02-12 08:27:44', '', 9500.00),
(19, 49, 4, 3, 2000.00, 9000.00, 9000.00, 0.00, 'Paid', '2025-02-13 06:26:00', 'Online', 11000.00),
(20, 50, 4, 3, 2000.00, 18000.00, 0.00, 0.00, 'Partially Paid', '2025-02-13 07:08:28', 'Bank Transfer', 2000.00),
(21, 51, 4, 3, 2000.00, 18000.00, 0.00, 0.00, 'Partially Paid', '2025-02-13 07:10:26', 'Bank Transfer', 2000.00),
(22, 52, 4, 3, 2000.00, 9000.00, 0.00, 0.00, 'Partially Paid', '2025-02-13 07:11:44', '', 11000.00);

--
-- Dumping data for table `register`
--

INSERT INTO `register` (`register_id`, `name`, `email`, `password`, `phone`, `created_at`, `active`) VALUES
(1, 'register', 'register@gmail.com', '$2y$10$z4F1bJQ.OKGZwK8.XwpAl.8sPOS8WjI/yT0XLoMBL8plM.CkKBHj2', '1122334455', '2025-01-29 06:53:49', 0),
(2, 'reshab', 'reshab@gmail.com', '$2y$10$10Nx9j3TITdxrWPyfCkQZeXt.n5spL0l5uEi.mdPG9Gw34./IUZdS', '112233445', '2025-01-31 06:25:21', 1),
(3, 'ashif', 'ashif@gmail.com', '$2y$10$0kkeJ3RhGDbqPzUkry.ZvOLdl9RuXMe1eNjL0CJuH6q/tAXNfPZHC', '1122334455', '2025-01-31 08:55:38', 0);

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `name`, `email`, `password`, `phone`, `address`, `registration_date`, `city`, `state`, `gender`) VALUES
(1, 'Daisy Goswami', 'daisy@example.com', '$2y$10$o3osa36EY6GlSTzkxVXiYOO31Lzty1/sxx7YofzHFvdfPM6eP3BIe', '9988776655', 'Kaliabor, Assam', '2025-01-29 14:21:07', NULL, NULL, 'Male'),
(2, 'daisy', 'daisy@gmail.com', '$2y$10$3NNlEmQsXBZR4ZhK54Cr1OD02kR9pUBVn5mWjemedHpqritA28Jl2', '7866778866', 'Karbi Anglong, Assam', '2025-01-31 14:04:32', 'Karbi Anglong', 'Assam', 'Female'),
(3, 'afrin', 'afrin@gmail.com', '$2y$10$CM7RZNUwHOud.xJviGHMWeHkxaCr1.QMBKzhxHnSRkSfuP0HzW8nW', '9988776655', 'Borpeta', '2025-01-31 14:05:30', 'Anantnag', 'Jammu & Kashmir', 'Female'),
(4, 'progyan', 'progyan@gmail.com', '$2y$10$81yIvVEGVnJVaeHP6jl1YOdC9sjDJ95vhXWBXR/xHg56T6duvH69a', '9988776655', 'Dibrugarh, Assam', '2025-01-31 14:27:52', 'Dibrugarh', 'Assam', 'Male'),
(5, 'Reshab Gupta', 'reshab@gmail.com', '$2y$10$xFW63A6XSDkPN05ywAHY5Oz3Ewf3yenBREWatwdUCFE7.PL9dtO52', '1122334455', 'Tezpur, Assam', '2025-02-06 14:00:18', 'Tezpur', 'Assam', 'Male'),
(6, 'demo', 'demo@gmail.com', '$2y$10$1YJeU1a86z3lA1eSBiDjWuOuXRdIejvp1ijhasxNRrOhqQmZBPmgW', '1122334455', 'Belgaum, Karnataka', '2025-02-07 13:40:14', 'Belgaum', 'Karnataka', 'Male'),
(7, 'demo2', 'demo2@gmail.com', '$2y$10$jHMjNHAAEqHqKJKXwwzDnusxxA7fADEy9OOWxaP5NeY7B.r8eo6Am', '5544332211', 'Gwalior, Madhya Pradesh', '2025-02-07 13:43:08', 'Gwalior', 'Madhya Pradesh', 'Male'),
(8, 'sdf', 'sdf@gmail.com', '$2y$10$qYKMOj/JvajUGT0E/opbJubTHcbP8/ins7DcjtbhxWlGibjJkIxf6', '1122334455', 'Gurgaon, Haryana', '2025-02-07 14:02:21', 'Gurgaon', 'Haryana', 'Male'),
(9, 'demo3', 'demo3@gmail.com', '$2y$10$/wfdPl51QVSMjqcaqAMnu.CUXpJ6pEvr4nYSsYXrueulrWnV9Cd2.', '1122334455', 'Chandigarh, Chandigarh', '2025-02-07 15:16:58', 'Chandigarh', 'Chandigarh', 'Male'),
(10, 'demo4', 'demo4@gmail.com', '$2y$10$hzneBLdirm/woGhJEwEr3.J6wXc7JZ/AuMfUfZYVIOwZ945WKeb2G', '1122334455', 'Margao, Goa', '2025-02-07 15:22:49', 'Margao', 'Goa', 'Male'),
(11, 'new ', 'new@gmail.com', '$2y$10$efLRrVJn/Dc54aZQcM.WEOjOZsLc90vi7lle6Bzv5fJ3F093AbZ1O', '1122334455', 'Ahmedabad, Gujarat', '2025-02-07 15:27:59', 'Ahmedabad', 'Gujarat', 'Male'),
(12, 'sanjeev', 'sanjeev@gmail.com', '$2y$10$SVo9ajbGZllllEfixR1qZOK/XFvaHbmnkab6fRxV/kf2ywpJ/vz6q', '5544332211', 'Nagpur, Maharashtra', '2025-02-07 15:30:11', 'Nagpur', 'Maharashtra', 'Male'),
(13, 'qwerty', 'qwerty@gmail.com', '$2y$10$x9YpGXbRYBW8yOK9N//ARurTTBml7Bx.Ohu4IWm2JZRbdIcWAT88W', '1122334455', 'Faridabad, Haryana', '2025-02-07 15:35:09', 'Faridabad', 'Haryana', 'Male'),
(19, 'afrin', 'afrin@yahoo.com', '$2y$10$tc82LcvbbqQ7HYCCI1e.O.NSQJN7hM1JiRlyP/ONMHtHZWlDoBoaO', '8899008899', 'Mangalore, Karnataka', '2025-02-11 13:16:17', 'Mangalore', 'Karnataka', 'Female'),
(20, 'simran', 'simran@gmail.com', '$2y$10$PucrFMw.ho.AVZZk/QR3BujyjmuMgeXHfEKnNKei.wJLuvHflIszG', '9988776655', 'Surat, Gujarat', '2025-02-11 13:21:06', 'Surat', 'Gujarat', 'Female'),
(23, 'rajib', 'rajib@gmail.com', '$2y$10$1sRokc5pQicC2pDByt1G0enIQNk43Xeqt8NkD7mreGejncJK0wMGu', '1122334455', 'Ahmedabad, Gujarat', '2025-02-11 14:10:05', 'Ahmedabad', 'Gujarat', 'Male'),
(24, 'shahid', 'shahid@gmail.com', '$2y$10$LvVrTpGSEsU3NrVIG4VEoO1kP5AHnGsxh714GjL4YE9CNIU2fL2fu', '1122334455', 'Jammu, Jammu & Kashmir', '2025-02-11 14:16:40', 'Jammu', 'Jammu & Kashmir', 'Male'),
(25, 'rashid', 'rashid@gmail.com', '$2y$10$T/Pwe.lXTf5gQr5lroyMeuK1yFxZ7OmXL1UDxkBJIlajByFi7dk4m', '1122334455', 'Surat, Gujarat', '2025-02-11 15:05:09', 'Surat', 'Gujarat', 'Male'),
(26, 'sahid', 'sahid@gmail.com', '$2y$10$/FxyKfm17ZyqGuw81K17bOTPdWdqIrUqaAQvATQ/WH66fYmh7BX9e', '1122334455', 'Ahmedabad, Gujarat', '2025-02-11 15:10:24', 'Ahmedabad', 'Gujarat', 'Male'),
(28, 'nilam', 'nilam@gmail.com', '$2y$10$KBodPVi3gomC081Qdf9O8.BEhvtLH.6RZeGQ14062i80qnlPjqiRy', '1122334455', 'Gurgaon, Haryana', '2025-02-11 15:15:31', 'Gurgaon', 'Haryana', 'Male'),
(30, 'kashyap', 'kashyap@gmail.com', '$2y$10$8njwopiMi/Dm2gGwKO8dDOr2PxCCTTgS1AJXYdIa/g8cMMylFYu9W', '2211334455', 'Durg, Chhattisgarh', '2025-02-11 19:16:42', 'Durg', 'Chhattisgarh', 'Male'),
(31, 'bhoomi', 'bhoomi@gmail.com', '$2y$10$wvGo1iwCD6CehnDr3m.qBu9IhV7LFe4SXtQ5U2Qhipf2BvryfLZeu', '7766554422', 'Saket, Delhi', '2025-02-11 19:34:05', 'Saket', 'Delhi', 'Female'),
(32, 'Javedi', 'javedi@gmail.com', '$2y$10$grDeeUmBgkkBM4Amj/Eb4.72vrP8m97T3BeJmq1bkmbJnVY.4NgTS', '8877665544', 'Dibrugarh, Assam', '2025-02-12 12:04:53', 'Dibrugarh', 'Assam', 'Female'),
(33, 'Ruma', 'ruma@gmail.com', '$2y$10$D4FmzsD3ICVSP2JN0s0JkewuwWYPD18crFwFe6Bi.fTjFC6EqP3uW', '9876789002', 'Thoubal, Manipur', '2025-02-12 12:09:40', 'Thoubal', 'Manipur', 'Female'),
(34, 'neha', 'neha@gmail.com', '$2y$10$j7jB1Fy0.hafzRK.siez3eI7NvC2pzNHaKSmdShu3xOhyBwNO1BDu', '1234512345', 'Thrissur, Kerala', '2025-02-12 12:35:21', 'Thrissur', 'Kerala', 'Female'),
(35, 'julfa', 'julfa@gmail.com', '$2y$10$YTx824QS6fiYWVBr6u2xdeGC282T17OPa8UTsHEvvmsNzCmN9HsMe', '9876987600', 'Dhanbad, Jharkhand', '2025-02-12 13:00:15', 'Dhanbad', 'Jharkhand', 'Female'),
(36, 'rekha', 'rekha@gmail.com', '$2y$10$OaASnIJRUEowTq5r/RDMpO/bNY2r/5Ew6QesZEHnXVrwNTykflYSe', '98765009988', 'Hailakandi, Assam', '2025-02-12 13:09:28', 'Hailakandi', 'Assam', 'Female'),
(37, 'gaurav', 'gaurav@gmail.com', '$2y$10$MtSIztBpWxrUXor5497qxuqOEbJzSNNrdIRju/iw3Tl2dJD6tHuNO', '98765009988', 'Lakhimpur, Assam', '2025-02-12 13:12:42', 'Lakhimpur', 'Assam', 'Male'),
(38, 'debraj', 'debraj@gmail.com', '$2y$10$1NYYzuNA14k2SFiVSXdC6u7uBOXLalGtye9On6urBwJ0Yn.v8fFKW', '98765009988', 'Mumbai, Maharashtra', '2025-02-12 13:27:20', 'Mumbai', 'Maharashtra', 'Male'),
(39, 'sohaib', 'sohaib@gmail.com', '$2y$10$xpRonZwvJQYoag7ffj6vbehJnRHxns/l84BWBNhmuo5u/Tw2IlCSu', '9876511223', 'Ahmedabad, Gujarat', '2025-02-12 13:28:55', 'Ahmedabad', 'Gujarat', 'Male'),
(40, 'krishna', 'krishna@gmail.com', '$2y$10$qDdVxLYIcKmDzw9fQKVWBu3G95/jWK6cY6UyooDoFS.zHleVUjZEK', '8978660112', 'Jamshedpur, Jharkhand', '2025-02-12 13:29:53', 'Jamshedpur', 'Jharkhand', 'Female'),
(41, 'reeta', 'reeta@gmail.com', '$2y$10$agd39Vzof62UEouHaCrXreOOKa.vyESicFo3XGb/c4yCMyQ37px.m', '9876511022', 'Manali, Himachal Pradesh', '2025-02-12 13:33:36', 'Manali', 'Himachal Pradesh', 'Female'),
(42, 'reetama', 'reetama@gmail.com', '$2y$10$ppZOrvBcu5EbArrmWmjjhOQ1ZOG8WpHXvwEJ9Y1RYyscxvtm0v/FG', '9876511011', 'Nagpur, Maharashtra', '2025-02-12 13:38:48', 'Nagpur', 'Maharashtra', 'Female'),
(43, 'reet', 'reet@gmail.com', '$2y$10$BRwkEoVOq4OYH.XaovNAbuzxKPtVsWCwNYWVWOQac4mrLshSXW6OW', '7876511011', 'Bhopal, Madhya Pradesh', '2025-02-12 13:39:55', 'Bhopal', 'Madhya Pradesh', 'Male'),
(44, 'reetika', 'reetika@gmail.com', '$2y$10$5hNOZB5z1NYfcURZmYCD7OJvSegAvPgR2fpiyrjahOmhMsQencveS', '8876511011', 'Ukhrul, Manipur', '2025-02-12 13:42:16', 'Ukhrul', 'Manipur', 'Female'),
(45, 'shivam', 'shivam@gmail.com', '$2y$10$xOVIWgvrx8kuwceP0q/5oestV1WkQAAG/t8LQpf.c4YRInBAbv0Lq', '7655667800', 'Haridwar, Uttarakhand', '2025-02-12 13:43:58', 'Haridwar', 'Uttarakhand', 'Male'),
(46, 'raj', 'raj@gmail.com', '$2y$10$8Jn63xFONwxTznU3fyqu6edn1kcihsZqTEoXk9/Pir9BGNFWnQHgG', '8876550112', 'Shillong, Meghalaya', '2025-02-12 13:51:42', 'Shillong', 'Meghalaya', 'Male'),
(47, 'zubin', 'zubin@gmail.com', '$2y$10$DMRGV7SdLWh5mMQ74M2QXOY8NE0gG/xtvj7FSDC5HauNjdRHLBAL.', '9088611922', 'Surat, Gujarat', '2025-02-12 13:52:13', 'Surat', 'Gujarat', 'Male'),
(48, 'radhika', 'radhika@gmail.com', '$2y$10$hKinNTDosx1ovQt8mSdktu8cw4PNas0LK9TOH951yG.sVu0zGvKdi', '9988711022', 'Saket, Delhi', '2025-02-12 13:57:44', 'Saket', 'Delhi', 'Other'),
(49, 'Seema', 'seema@gmail.com', '$2y$10$C8Sy.vLQQM40nro6sOQne.InLdUYUKHQpq50xIN7V3EUu3nuHmdQS', '9876544100', 'Morigaon, Assam', '2025-02-13 11:56:00', 'Morigaon', 'Assam', 'Female'),
(50, 'messi', 'messi@gmail.com', '$2y$10$Kln874S./8/wTJqhF/150uMjD6djIRUVUqavSme0B/6FXxnt5bKEO', '9877665544', 'Kolkata, West Bengal', '2025-02-13 12:38:28', 'Kolkata', 'West Bengal', 'Other'),
(51, 'heema', 'heema@gmail.com', '$2y$10$zPNW/auapZKpQU/gqtkMNem01ji6MBfQuRZ9Ikzzw4kikmA3Oe2r6', '9876545612', 'Thrissur, Kerala', '2025-02-13 12:40:26', 'Thrissur', 'Kerala', 'Female'),
(52, 'heera', 'heera@gmail.com', '$2y$10$aDQpAjkNM6ctklXAuJQHJu6gR7zDtOtWmtHrrAH1d8uY05AzUEVWe', '9876545611', 'Thrissur, Kerala', '2025-02-13 12:41:44', 'Thrissur', 'Kerala', 'Male');

--
-- Dumping data for table `student_batches`
--

INSERT INTO `student_batches` (`id`, `student_id`, `batch_id`, `registration_status`, `booking_date`, `payment_status`) VALUES
(1, 1, 1, 'Pending', '2025-02-11 14:34:54', 'Pending'),
(2, 2, 2, 'Pending', '2025-02-11 14:34:54', 'Pending'),
(3, 3, 2, 'Pending', '2025-02-11 14:34:54', 'Pending'),
(4, 3, 1, 'Pending', '2025-02-11 14:34:54', 'Pending'),
(5, 4, 1, 'Pending', '2025-02-11 14:34:54', 'Pending'),
(6, 5, 1, 'Pending', '2025-02-11 14:34:54', 'Pending'),
(7, 6, 2, 'Pending', '2025-02-11 14:34:54', 'Pending'),
(8, 7, 1, 'Pending', '2025-02-11 14:34:54', 'Pending'),
(9, 8, 1, 'Pending', '2025-02-11 14:34:54', 'Pending'),
(10, 9, 1, 'Pending', '2025-02-11 14:34:54', 'Pending'),
(11, 10, 1, 'Pending', '2025-02-11 14:34:54', 'Pending'),
(12, 11, 1, 'Pending', '2025-02-11 14:34:54', 'Pending'),
(13, 12, 1, 'Pending', '2025-02-11 14:34:54', 'Pending'),
(14, 13, 1, 'Pending', '2025-02-11 14:34:54', 'Pending'),
(15, 19, 2, 'Pending', '2025-02-11 14:34:54', 'Pending'),
(16, 20, 1, 'Pending', '2025-02-11 14:34:54', 'Pending'),
(17, 23, 2, 'Pending', '2025-02-11 14:34:54', 'Pending'),
(18, 24, 1, 'Pending', '2025-02-11 14:34:54', 'Pending'),
(19, 25, 1, 'Booked', '2025-02-11 15:05:09', 'Pending'),
(20, 26, 2, 'Booked', '2025-02-11 15:10:24', 'Pending'),
(21, 28, 1, 'Booked', '2025-02-11 15:15:31', 'Pending'),
(22, 30, 1, 'Booked', '2025-02-11 19:16:42', 'Pending'),
(23, 31, 2, 'Booked', '2025-02-11 19:34:05', 'Pending'),
(24, 32, 1, 'Booked', '2025-02-12 12:04:53', 'Pending'),
(25, 33, 2, 'Booked', '2025-02-12 12:09:40', 'Pending'),
(26, 34, 2, 'Booked', '2025-02-12 12:35:21', 'Pending'),
(27, 35, 1, 'Booked', '2025-02-12 13:00:15', 'Pending'),
(28, 36, 1, 'Booked', '2025-02-12 13:09:28', 'Pending'),
(29, 37, 1, 'Booked', '2025-02-12 13:12:42', 'Pending'),
(30, 38, 1, 'Booked', '2025-02-12 13:27:20', 'Pending'),
(31, 39, 1, 'Booked', '2025-02-12 13:28:55', 'Pending'),
(32, 40, 2, 'Booked', '2025-02-12 13:29:53', 'Pending'),
(33, 41, 2, 'Booked', '2025-02-12 13:33:36', 'Pending'),
(34, 42, 1, 'Booked', '2025-02-12 13:38:48', 'Pending'),
(35, 43, 2, 'Booked', '2025-02-12 13:39:55', 'Pending'),
(36, 44, 1, 'Booked', '2025-02-12 13:42:16', 'Pending'),
(37, 45, 1, 'Booked', '2025-02-12 13:43:58', 'Pending'),
(38, 46, 1, 'Booked', '2025-02-12 13:51:42', 'Pending'),
(39, 47, 1, 'Booked', '2025-02-12 13:52:13', 'Pending'),
(40, 48, 2, 'Booked', '2025-02-12 13:57:44', 'Pending'),
(41, 49, 3, 'Booked', '2025-02-13 11:56:00', 'Pending'),
(42, 50, 3, 'Booked', '2025-02-13 12:38:28', 'Pending'),
(43, 51, 3, 'Booked', '2025-02-13 12:40:26', 'Pending'),
(44, 52, 3, 'Booked', '2025-02-13 12:41:44', 'Pending');

--
-- Dumping data for table `super_admins`
--

INSERT INTO `super_admins` (`super_admin_id`, `username`, `email`, `password`) VALUES
(1, 'superadmin', 'superadmin@example.com', '$2y$10$my66MLWQ4GF.85yKhjsNvOnEkn6H7RAmV3SfohATkL5ZDLBGwm2uS');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
