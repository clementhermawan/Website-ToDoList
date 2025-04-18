-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 05 Apr 2025 pada 11.56
-- Versi server: 10.4.20-MariaDB
-- Versi PHP: 8.4.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `todo_list`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `reminders`
--

CREATE TABLE `reminders` (
  `id` int(11) NOT NULL,
  `task_id` int(11) DEFAULT NULL,
  `reminder_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `reminders`
--

INSERT INTO `reminders` (`id`, `task_id`, `reminder_time`) VALUES
(1, 1, '2025-04-09 15:00:00'),
(2, 2, '2025-04-04 08:00:00'),
(3, 3, '2025-04-05 06:00:00'),
(4, 4, '2025-04-06 10:00:00'),
(5, 5, '2025-04-07 13:00:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `schedule`
--

CREATE TABLE `schedule` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `task_id` int(11) DEFAULT NULL,
  `schedule_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `schedule`
--

INSERT INTO `schedule` (`id`, `user_id`, `task_id`, `schedule_time`) VALUES
(1, 1, 1, '2025-04-09 16:00:00'),
(2, 2, 2, '2025-04-04 09:00:00'),
(3, 3, 3, '2025-04-05 07:00:00'),
(4, 1, 4, '2025-04-06 11:00:00'),
(5, 2, 5, '2025-04-07 14:00:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sessions`
--

CREATE TABLE `sessions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `session_token` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `task_name` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `deadline` datetime DEFAULT NULL,
  `status` enum('pending','completed') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `tasks`
--

INSERT INTO `tasks` (`id`, `user_id`, `task_name`, `description`, `deadline`, `status`) VALUES
(1, 1, 'Menyelesaikan proyek PHP', 'Selesaikan proyek web menggunakan PHP dan MySQL.', '2025-04-10 17:00:00', 'pending'),
(2, 2, 'Beli bahan makanan', 'Beli nasi, sayuran, dan ayam di pasar.', '2025-04-04 10:00:00', 'completed'),
(3, 3, 'Pekerjaan rumah matematika', 'Selesaikan soal matematika untuk kelas besok.', '2025-04-05 08:00:00', 'pending'),
(4, 1, 'Buat laporan mingguan', 'Membuat laporan mingguan untuk pekerjaan.', '2025-04-06 12:00:00', 'completed'),
(5, 5, 'Ambil laundry', 'Ambil pakaian yang sudah dicuci di laundry.', '2025-04-07 15:00:00', 'completed'),
(6, 5, 'belajar motor', 'asdasdasdadas', '2025-04-03 21:47:00', 'completed'),
(7, 5, 'UKK', 'UKK', '2025-04-03 22:49:00', 'completed'),
(8, 5, 'asdasdsa', 'asdasdasdas', '2025-04-03 22:49:00', 'completed'),
(9, 4, 'belajar motor', 'belajar motor', '2025-04-04 00:27:00', 'pending'),
(10, 4, 'asdasdas', 'asdasdsadsaa', '2025-04-04 00:29:00', 'completed'),
(11, 5, 'asdasdas', 'asdasdasda', '2025-04-04 00:32:00', 'completed'),
(12, 5, 'dasdasdasdas', 'asdasdasdasd', '2025-04-04 00:34:00', 'completed'),
(13, 5, 'xczxc', 'zxczxczx', '2025-04-04 01:35:00', 'completed'),
(14, 5, 'ddadsadas', 'dasdasdasdas', '2025-04-05 04:42:00', 'completed'),
(15, 5, 'Belajar', 'Belajar', '2025-04-04 00:40:00', 'completed'),
(21, 5, 'UKK PART 1', 'UKK PART 1', '2025-04-03 13:39:00', 'completed'),
(22, 5, 'sdasdasd', 'asasdasdasdas', '2025-04-04 13:42:00', 'pending'),
(23, 5, 'adsadasdsadas', 'asdadasdasdasa', '2025-04-04 13:45:00', 'pending');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`) VALUES
(1, 'budi_susanto', 'hashed_password_budi', 'budi@example.com'),
(2, 'siti_amalia', 'hashed_password_siti', 'siti@example.com'),
(3, 'anto_prasetyo', 'hashed_password_anto', 'anto@example.com'),
(4, 'Clement', '$2y$10$8w8vtj4wZxd5v73tS5DFrO9OvwYFXuDL1.gss0sOZBYvRM5GFFiY6', 'clement@example.com'),
(5, 'clay', '$2y$10$xBh2h6Z.FPRL/j5OsczyS.akRdQxgh3JTmNYj08ayNZeENFYPmdme', 'clay@example.com');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `reminders`
--
ALTER TABLE `reminders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_id` (`task_id`);

--
-- Indeks untuk tabel `schedule`
--
ALTER TABLE `schedule`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `task_id` (`task_id`);

--
-- Indeks untuk tabel `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `reminders`
--
ALTER TABLE `reminders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `schedule`
--
ALTER TABLE `schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT untuk tabel `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `reminders`
--
ALTER TABLE `reminders`
  ADD CONSTRAINT `reminders_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `schedule`
--
ALTER TABLE `schedule`
  ADD CONSTRAINT `schedule_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `schedule_ibfk_2` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `sessions`
--
ALTER TABLE `sessions`
  ADD CONSTRAINT `sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
