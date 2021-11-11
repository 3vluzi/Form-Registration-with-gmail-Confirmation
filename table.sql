
CREATE TABLE `tbl_daftar` 
(
`id` int(11) NOT NULL,
`no_daftar` text NOT NULL,
`nama` text NOT NULL,
`alamat_email` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
--
-- Indexes for dumped tables
--
--
-- Indexes for table `tbl_daftar`
--
ALTER TABLE `tbl_daftar`
  ADD PRIMARY KEY (`id`);
--
-- AUTO_INCREMENT for dumped tables
--
--
-- AUTO_INCREMENT for table `tbl_daftar`
--
ALTER TABLE `tbl_daftar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;
