-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 03, 2025 at 03:17 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hospital_inventory`
--

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

CREATE TABLE `assignments` (
  `id` int(11) NOT NULL,
  `drug_id` int(11) DEFAULT NULL,
  `patient_name` varchar(255) DEFAULT NULL,
  `patient_contact` varchar(255) DEFAULT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `association` varchar(255) NOT NULL,
  `role` enum('Patient','Worker') NOT NULL,
  `salary` decimal(10,2) DEFAULT NULL,
  `registration_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `email`, `phone`, `association`, `role`, `salary`, `registration_date`) VALUES
(2, 'steve cascallen', 'cascallensteve@gmail.com', '0793515066', 'doctor', 'Worker', 12000.00, '2025-01-26');

-- --------------------------------------------------------

--
-- Table structure for table `drug`
--

CREATE TABLE `drug` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `disease_treated` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `drug`
--

INSERT INTO `drug` (`id`, `name`, `description`, `disease_treated`, `price`, `added_at`) VALUES
(1, 'cascallen', 'gghjj', 'k', 4556.00, '2025-01-21 07:31:34'),
(2, 'cascallen', 'gghjj', 'k', 4556.00, '2025-01-21 07:31:55'),
(3, 'cascallen', 'gghjj', 'k', 4556.00, '2025-01-21 07:33:14');

-- --------------------------------------------------------

--
-- Table structure for table `drugs`
--

CREATE TABLE `drugs` (
  `drug_id` int(11) NOT NULL,
  `drug_name` varchar(100) NOT NULL,
  `drug_category` varchar(100) NOT NULL,
  `drug_quantity` int(11) NOT NULL,
  `drug_price` decimal(10,2) NOT NULL,
  `available_quantity` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `drugs`
--

INSERT INTO `drugs` (`drug_id`, `drug_name`, `drug_category`, `drug_quantity`, `drug_price`, `available_quantity`) VALUES
(2, 'diclofenack', 's566', 23, 45.00, 0),
(3, 'sde', '', 0, 0.00, 0),
(4, 'diclofenack', '', 0, 0.00, 0);

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `patient_name` varchar(255) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `invoice_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Paid','Unpaid') DEFAULT 'Unpaid'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `patient_name`, `medicine_id`, `quantity`, `unit_price`, `total_amount`, `invoice_date`, `status`) VALUES
(1, 'Shadrack otieno', 4, 423, 123.00, 52029.00, '2025-01-22 12:47:43', 'Unpaid'),
(2, 'james', 4, 423, 123.00, 52029.00, '2025-01-22 12:48:18', 'Unpaid'),
(4, 'sttevve', 4, 1, 123.00, 123.00, '2025-01-22 12:51:27', 'Unpaid');

-- --------------------------------------------------------

--
-- Table structure for table `medications`
--

CREATE TABLE `medications` (
  `id` int(11) NOT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `dosage` varchar(255) DEFAULT NULL,
  `quantity` int(11) DEFAULT 0,
  `price` decimal(10,2) DEFAULT NULL,
  `expiration_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `medicines`
--

CREATE TABLE `medicines` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `medicines_drugs`
--

CREATE TABLE `medicines_drugs` (
  `id` int(11) NOT NULL,
  `medicine_name` varchar(255) NOT NULL,
  `packing` varchar(100) DEFAULT NULL,
  `generic_name` varchar(255) DEFAULT NULL,
  `batch_id` varchar(100) DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `supplier` varchar(255) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `mrp` decimal(10,2) DEFAULT NULL,
  `rate` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medicines_drugs`
--

INSERT INTO `medicines_drugs` (`id`, `medicine_name`, `packing`, `generic_name`, `batch_id`, `expiry_date`, `supplier`, `quantity`, `mrp`, `rate`) VALUES
(2, 'Paracetamol (Acetaminophen)', '12', 'Acetaminophen', 'P-12345', '2025-01-22', 'james', 423, 10.00, 0.00),
(3, 'Ibuprofen', '12', 'Ibuprofen', 'P-12345', '0000-00-00', 'james', 123, 32.00, 5.00),
(7, 'diclofenac', 'as', 'ded', 'P-12345', '2025-01-30', 'james', 423, 123.00, 123.00);

-- --------------------------------------------------------

--
-- Table structure for table `med_drug`
--

CREATE TABLE `med_drug` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `expiry_date` date NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `med_drug`
--


 INSERT INTO `med_drug` (`id`, `name`, `description`, `expiry_date`, `quantity`) VALUES
(4, 'paracetamol', 'dq', '2025-01-22', 12),
(5, 'cascallen', 'ssa', '2025-01-30', 423),
(6, 'cascallen', 'ssa', '2025-01-30', 423);

-- --------------------------------------------------------

--
-- Table structure for table `prescriptions`
--

CREATE TABLE `prescriptions` (
  `id` int(11) NOT NULL,
  `client_name` varchar(255) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `dose` varchar(255) NOT NULL,
  `duration` varchar(255) NOT NULL,
  `instructions` text NOT NULL,
  `age` int(11) NOT NULL,
  `disease_type` varchar(255) NOT NULL,
  `prescription_datetime` timestamp NOT NULL DEFAULT current_timestamp(),
  `prescription_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prescriptions`
--

INSERT INTO `prescriptions` (`id`, `client_name`, `medicine_id`, `dose`, `duration`, `instructions`, `age`, `disease_type`, `prescription_datetime`, `prescription_date`) VALUES
(1, 'steve cascallen', 2, '2', '12', 'please', 0, '', '2025-01-28 09:37:15', NULL),
(2, 'steve cascallen', 2, '2', '12', 'please', 0, '', '2025-01-28 09:37:15', NULL),
(3, 'XWD', 2, 'S12E', 'E12', 'D23F', 12, 'D', '2025-01-28 09:38:03', NULL),
(4, 'JAMES ONDIEK', 3, '2', '4', '1234', 2233, 'MALARIA', '2025-01-28 09:43:09', NULL),
(5, 'JAMES ONDIEK', 3, '2', '4', '1234', 2233, 'MALARIA', '2025-01-28 09:44:53', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

CREATE TABLE `purchases` (
  `id` int(11) NOT NULL,
  `medicine_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `purchase_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `report_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `contact_info` varchar(255) NOT NULL,
  `license_number` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`, `address`, `contact_info`, `license_number`, `email`) VALUES
(3, 'cascallen', 'P.O. BOX 64', '123', '23', ''),
(4, 'cascallen', 'P O Box 34567', '123', '23', ''),
(9, 'Cascallen Steve', 'P.O. BOX 64', '123', 'q234', ''),
(10, 'Cascallen Steve', 'P.O. BOX 64', '123', 'q234', ''),
(11, 'Cascallen Steve', 'P.O. BOX 64', '123', 'q234', ''),
(12, 'Cascallen Steve', 'P.O. BOX 64', '123', 'q234', '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`) VALUES
(7, 'otieno steve owiti', 'DenisKip', 'cascallensteve@gmail.com'),
(8, 'Junior', 'Junior123', 'cascallensteve@gmail.com'),
(9, '', '12345678', 'cascallensteve9@gmail.com'),
(10, 'james otieno', 'cascallensteve4', 'cascallensteve9@gmail.com'),
(11, '', '12345678', 'cascallensteve9@gmail.com'),
(12, 'cascallen', 'stevoh2020', 'cascallensteve@gmail.com'),
(13, 'Junior Cajuzoh', '123456768', 'cajuzoh@gmail.com');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `drug_id` (`drug_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `'customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `drug`
--
ALTER TABLE `drug`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `drugs`
--
ALTER TABLE `drugs`
  ADD PRIMARY KEY (`drug_id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `medicine_id` (`medicine_id`);

--
-- Indexes for table `medications`
--
ALTER TABLE `medications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `medicines`
--
ALTER TABLE `medicines`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `medicines_drugs`
--
ALTER TABLE `medicines_drugs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `med_drug`
--
ALTER TABLE `med_drug`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `medicine_id` (`medicine_id`);

--
-- Indexes for table `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `medicine_id` (`medicine_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `drug`
--
ALTER TABLE `drug`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `drugs`
--
ALTER TABLE `drugs`
  MODIFY `drug_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `medications`
--
ALTER TABLE `medications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `medicines`
--
ALTER TABLE `medicines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `medicines_drugs`
--
ALTER TABLE `medicines_drugs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `med_drug`
--
ALTER TABLE `med_drug`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `prescriptions`
--
ALTER TABLE `prescriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assignments`
--
ALTER TABLE `assignments`
  ADD CONSTRAINT `assignments_ibfk_1` FOREIGN KEY (`drug_id`) REFERENCES `drugs` (`drug_id`);

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`medicine_id`) REFERENCES `med_drug` (`id`);

--
-- Constraints for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD CONSTRAINT `prescriptions_ibfk_1` FOREIGN KEY (`medicine_id`) REFERENCES `medicines_drugs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchases`
--
ALTER TABLE `purchases`
  ADD CONSTRAINT `purchases_ibfk_1` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
