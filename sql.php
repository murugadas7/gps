CREATE TABLE `list_devices` (
  `id` int(11) NOT NULL,
  `device_id` varchar(55) NOT NULL,
  `device_label` varchar(55) NOT NULL,
  `last_reported` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `list_devices`
--

INSERT INTO `list_devices` (`id`, `device_id`, `device_label`, `last_reported`) VALUES
(1, '100001', 'Van No 1', '2017-12-31 22:44:25'),
(2, '100002', 'Truck No 1', '2017-12-31 06:49:07'),
(3, '100003', 'Van No 2', '2018-01-01 00:14:05'),
(4, '100004', 'Truck No 2', '2018-01-01 00:36:07'),
(5, '100005', 'Van No 3', '2017-12-19 18:44:25'),
(6, '100006', 'Van No 4', '2017-12-09 20:44:25'),
(7, '100001', 'Van No 5', '2017-12-31 20:44:25'),
(8, '100003', 'Van No 6', '2017-12-31 23:44:05');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `list_devices`
--
ALTER TABLE `list_devices`
  ADD PRIMARY KEY (`id`);
 
ALTER TABLE `list_devices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;