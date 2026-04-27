-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 27, 2026 at 11:11 PM
-- Wersja serwera: 10.4.32-MariaDB
-- Wersja PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `film_reviews`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(57, 'Nieokreślona'),
(58, 'Science fiction'),
(60, 'Komedia'),
(61, 'Animacja'),
(64, 'Biograficzny'),
(65, 'Familijny'),
(66, 'Horror'),
(67, 'testty'),
(69, 'safsdfsdf');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `films`
--

CREATE TABLE `films` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `release_year` year(4) DEFAULT NULL,
  `poster` varchar(255) DEFAULT NULL,
  `category` int(11) DEFAULT NULL,
  `duration` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Dumping data for table `films`
--

INSERT INTO `films` (`id`, `title`, `description`, `release_year`, `poster`, `category`, `duration`) VALUES
(39, 'Avengers: Koniec gryy', 'Po wymazaniu połowy życia we Wszechświecie przez Thanosa Avengersi starają się zrobić wszystko, co konieczne, aby pokonać szalonego tytana.', '2015', 'uploads/film_678077565fb612.84244748_ooooooooo.PNG', 67, 182),
(40, 'Listy do M. 2', 'Po czterech latach ci sami bohaterowie borykają się z kolejnymi problemami.', '2015', 'uploads/677c223c4551c_listy.jpg', 60, 103),
(41, 'Shrek', 'By odzyskać swój dom, brzydki ogr z gadatliwym osłem wyruszają uwolnić piękną księżniczkę.', '2001', 'uploads/film_677c8ffcad4920.45856200_shrek.jpg', 61, 90),
(42, 'Titanic', 'Rok 1912, brytyjski statek Titanic wyrusza w swój dziewiczy rejs do USA. Na pokładzie emigrant Jack przypadkowo spotyka arystokratkę Rose.', '1997', 'uploads/film_677c900322faa6.49233326_titanic.jpg', 57, 194),
(43, 'Skazani na Shawshank', 'Adaptacja opowiadania Stephena Kinga. Niesłusznie skazany na dożywocie bankier, stara się przetrwać w brutalnym, więziennym świecie.', '1994', 'uploads/film_677c8ff57bef40.13797520_shawshank.jpg', 57, 144),
(44, 'Indiana Jones', 'Słynny archeolog i miłośnik przygód Indiana Jones oraz Mutt Williams poszukują legendarnego artefaktu.', '2008', 'uploads/film_677c8fd2612f38.88984664_indiana.jpeg', 57, 122),
(45, 'Auta', 'Ambitny samochód wyścigowy przypadkiem trafia do małego miasteczka, gdzie wyrządza znaczne szkody. Zanim opuści to miejsce, będzie musiał wszystko naprawić', '2006', 'uploads/film_677c8f1b259c47.89091580_auta.PNG', 61, 117),
(46, 'Nietyklani', 'Sparaliżowany milioner zatrudnia do opieki młodego chłopaka z przedmieścia, który właśnie wyszedł z więzienia.', '2011', 'uploads/film_677c8feed0f8b6.65658747_nietykalni.PNG', 64, 112),
(47, 'Zielona mila', 'Emerytowany strażnik więzienny opowiada przyjaciółce o niezwykłym mężczyźnie, którego skazano na śmierć za zabójstwo dwóch 9-letnich dziewczynek.', '1999', 'uploads/film_677c900dbbf098.62013475_zielona_mila.jpg', 57, 188),
(48, 'Król lew', 'W wyniku podstępu Skazy prawowity władca afrykańskiej sawanny, Simba, zostaje wygnany. Razem z dwójką przyjaciół zamierza odzyskać tron.', '2019', 'uploads/film_677c8fdde13600.28418614_krol_lew.jpg', 65, 118),
(49, 'Bambi', 'Jelonek Bambi beztrosko spędza każdy dzień. Pierwsze spotkanie z ludźmi kończy się śmiercią jego matki.', '1942', 'uploads/film_677c8fb8c45314.78906618_bambi.PNG', 61, 70),
(50, 'Forrest Gump', 'Historia życia Forresta, chłopca o niskim ilorazie inteligencji z niedowładem kończyn, który staje się miliarderem i bohaterem wojny w Wietnamie.', '1994', 'uploads/film_677c8fc1abed97.18375618_forest_gump.PNG', 57, 144),
(51, 'Avatar', 'Jake, sparaliżowany były komandos, zostaje wysłany na planetę Pandora, gdzie zaprzyjaźnia się z lokalną społecznością i postanawia jej pomóc.', '2009', 'uploads/677c26f1653e4_avatar.PNG', 58, 162),
(54, 'Everest', 'Rok 1996. Podczas wspinaczki na najwyższy szczyt świata członkowie ekspedycji stawiają czoło potężnej burzy śnieżnej.', '2015', 'uploads/film_677c7e17b9ff02.43405361_everest.PNG', 57, 121),
(55, 'Lśnienie', 'Jack podejmuje pracę stróża odciętego od świata hotelu Overlook. Wkrótce idylla zamienia się w koszmar.', '1980', 'uploads/film_677c8fe76d2660.63940982_lsnienie.PNG', 66, 146),
(65, 'Młody Frankenstein', 'Wnuczek doktora Frankensteina dziedziczy majątek oraz tajemnice szalonych eksperymentów dziadka.', '1974', 'uploads/film_677c8ecdf0db42.69858582_mlody_frankenstein.PNG', 66, 106),
(66, 'Dracula', 'Rumuński książę, a w rzeczywistości wampir Vlad Dracula wyjeżdża do Londynu, gdzie mieszka kobieta przypominająca mu jego tragicznie utraconą ukochaną.', '1992', 'uploads/film_677c8f8b113570.81861113_dracula.PNG', 66, 128),
(67, 'Top Gun: Maverick', 'Po ponad 20 latach służby w lotnictwie marynarki wojennej, Pete \"Maverick\" Mitchell zostaje wezwany do legendarnej szkoły Top Gun. Ma wyszkolić nowe pokolenie pilotów do niezwykle trudnej misji.', '2022', 'uploads/film_677c90c5870674.74696375_top_maverick.PNG', 57, 131),
(68, 'Joker', 'Strudzony życiem komik popada w obłęd i staje się psychopatycznym mordercą.', '2019', 'uploads/film_677c9115106949.54875327_joker.PNG', 57, 123),
(69, 'Piękny umysł', 'Geniusz matematyczny John Nash za wszelką cenę pragnie opracować teorię, dzięki której zostanie cenionym naukowcem. Przeszkodą staje się jego stopniowo rozwijająca choroba.', '2001', 'uploads/film_677c9198851c40.47498500_piekny_umysl.PNG', 64, 135),
(70, 'Oppenheimer', 'Historia amerykańskiego naukowca J. Roberta Oppenheimera i jego roli w stworzeniu bomby atomowej.', '2023', 'uploads/film_677c91dd37b011.11348322_oppenheimer.PNG', 64, 180),
(71, 'Bohemian Rhapsody', 'Dzięki oryginalnemu brzmieniu Queen staje się jednym z najpopularniejszych zespołów w historii muzyki.', '2018', 'uploads/film_677c9288c8f6c0.70461209_bohemian_rhapsody.PNG', 64, 134),
(72, 'Green Book', 'Drobny cwaniaczek z Bronksu zostaje szoferem ekstrawaganckiego muzyka z wyższych sfer i razem wyruszają na wielotygodniowe tournée.', '2018', 'uploads/film_677c92e61bc221.18291682_greenbook.PNG', 60, 130),
(73, 'Pianista', 'Podczas drugiej wojny światowej Władysław Szpilman, znakomity polski pianista, stara się przeżyć w okupowanej Warszawie.', '2002', 'uploads/film_677c93a795a5c5.63263917_pianista.PNG', 57, 150),
(74, 'Gwiezdne wojny: Ostatni Jedi', 'Rey odnajduje Luke\'a Skywalkera, by namówić go na powrót i walkę z Najwyższym Porządkiem. Tymczasem Rebelianci próbują uciec przed flotą wroga.', '2017', 'uploads/film_677c940e235209.70967482_starwars_ostatni.PNG', 58, 152),
(75, 'erer', 'erer', '2000', 'uploads/film_6780726ec6ed19.94450280_ooooooooo.PNG', 66, 3),
(76, 'dasdasd', 'dasdasd', '2000', 'uploads/film_67840b3a69f5f1.28493913_ooooooooo.PNG', 57, 266),
(77, 'Interstellar', 'Byt ludzkości na Ziemi dobiega końca wskutek zmian klimatycznych. Grupa naukowców odkrywa tunel czasoprzestrzenny, który umożliwia poszukiwanie nowego domu.', '2014', NULL, 58, 169),
(78, 'dasdsad', 'asdasdasd', '2000', NULL, 67, 2),
(79, 'dsadsadasdasdsa', 'dsadsadasdasdsa', '1999', NULL, 66, 3),
(80, 'dassaddddd', 'dsadsaa', '1998', NULL, 58, 2),
(81, 'asdasd', 'asdsa', '2000', NULL, 64, 3);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `film_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `rating` tinyint(3) UNSIGNED NOT NULL,
  `review_text` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `likes` int(11) DEFAULT 0,
  `dislikes` int(11) DEFAULT 0,
  `last_edited_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `film_id`, `user_id`, `rating`, `review_text`, `created_at`, `likes`, `dislikes`, `last_edited_at`) VALUES
(58, 39, 25, 5, 'Mój ulubiony!', '2025-01-07 02:56:33', 4, 0, NULL),
(59, 48, 25, 1, 'Bajka lepsza.', '2025-01-07 02:56:41', 2, 3, NULL),
(61, 72, 25, 3, 'może być', '2025-01-07 02:56:59', 1, 0, NULL),
(62, 74, 25, 4, 'spoko', '2025-01-07 02:57:06', 0, 1, NULL),
(63, 51, 25, 5, 'mocne kino', '2025-01-07 02:57:16', 1, 0, NULL),
(64, 40, 25, 2, 'haha!', '2025-01-07 02:57:26', 0, 1, '2025-01-07 04:24:26'),
(65, 54, 25, 4, 'ładne widoki, polecam', '2025-01-07 02:57:38', 1, 0, NULL),
(66, 46, 25, 5, 'super! film daje do myślenia.', '2025-01-07 02:57:53', 0, 0, NULL),
(67, 69, 25, 4, 'ciekawa historia', '2025-01-07 02:58:08', 0, 0, NULL),
(68, 49, 25, 5, 'kiedyś to były bajki :)', '2025-01-07 02:58:52', 0, 0, NULL),
(69, 55, 25, 4, 'wow', '2025-01-07 02:59:03', 0, 0, NULL),
(70, 47, 25, 5, 'wspaniała historia', '2025-01-07 02:59:11', 0, 0, NULL),
(71, 42, 25, 5, 'płakałam', '2025-01-07 02:59:22', 1, 0, NULL),
(72, 41, 25, 5, 'mój ulubiony film!', '2025-01-07 02:59:32', 1, 0, NULL),
(73, 45, 25, 4, 'super bajka do obejrzenia z rodzinką!!!! :)', '2025-01-07 02:59:51', 0, 0, NULL),
(74, 43, 26, 5, 'FAJNY', '2025-01-07 03:01:13', 0, 0, NULL),
(75, 44, 26, 1, 'Słaby', '2025-01-07 03:01:18', 0, 0, NULL),
(76, 50, 26, 3, 'Ciekawa historia', '2025-01-07 03:01:26', 0, 0, NULL),
(77, 65, 26, 4, '', '2025-01-07 03:01:37', 0, 0, NULL),
(78, 66, 26, 4, 'straszny', '2025-01-07 03:01:44', 0, 0, NULL),
(79, 67, 26, 5, 'I to się nazywa kino akcji!\r\nmój ulubiony aktor', '2025-01-07 03:02:11', 2, 3, NULL),
(80, 71, 26, 5, 'polecam', '2025-01-07 03:02:27', 1, 1, NULL),
(81, 73, 26, 5, '', '2025-01-07 03:02:33', 0, 0, NULL),
(82, 40, 26, 1, 'nie śmieszne', '2025-01-07 03:02:42', 2, 0, NULL),
(84, 49, 26, 4, '', '2025-01-07 03:02:52', 0, 0, NULL),
(85, 41, 26, 5, '', '2025-01-07 03:02:57', 0, 0, NULL),
(86, 54, 26, 1, '', '2025-01-07 03:03:01', 2, 0, NULL),
(87, 51, 26, 5, 'polecam z rodzinką!', '2025-01-07 03:03:17', 1, 1, NULL),
(88, 70, 27, 5, 'WOW! Najlepszy film tego roku. POLECAM wszystkim', '2025-01-07 03:04:30', 3, 1, NULL),
(89, 67, 27, 5, 'super film!', '2025-01-07 03:04:41', 3, 2, '2025-01-19 02:35:28'),
(90, 71, 27, 1, '', '2025-01-07 03:06:50', 0, 2, NULL),
(91, 74, 27, 3, '', '2025-01-07 03:06:55', 1, 0, NULL),
(92, 72, 27, 1, '', '2025-01-07 03:07:00', 0, 0, NULL),
(94, 48, 27, 5, '', '2025-01-07 03:07:53', 3, 1, NULL),
(95, 51, 27, 3, '', '2025-01-07 03:08:01', 1, 0, NULL),
(96, 55, 27, 4, '', '2025-01-07 03:08:10', 0, 0, NULL),
(97, 49, 27, 2, '', '2025-01-07 03:08:17', 0, 0, NULL),
(98, 40, 27, 4, '', '2025-01-07 03:08:24', 1, 0, NULL),
(99, 66, 27, 5, '', '2025-01-07 03:08:31', 0, 0, NULL),
(100, 50, 27, 4, '', '2025-01-07 03:08:39', 0, 0, NULL),
(101, 46, 27, 2, '', '2025-01-07 03:08:47', 0, 0, NULL),
(102, 70, 29, 5, 'Wybitne kino, super obsada, super efekty, super muzyka\r\npolecam wszystkim', '2025-01-07 03:09:38', 4, 1, NULL),
(103, 71, 29, 3, '', '2025-01-07 03:09:51', 0, 1, NULL),
(104, 74, 29, 3, 'poprzednie częsci zdecydowanie lepsze.', '2025-01-07 03:10:08', 0, 0, NULL),
(105, 54, 29, 4, 'Widziałem w kinie. Film był ok', '2025-01-07 03:10:39', 1, 1, NULL),
(107, 42, 32, 5, 'super!', '2025-01-07 03:13:27', 2, 0, NULL),
(110, 39, 34, 3, '', '2025-01-07 03:16:10', 0, 0, NULL),
(111, 70, 34, 5, 'oglądałam 3 razy, super film', '2025-01-07 03:16:25', 1, 1, '2025-01-07 04:20:29'),
(112, 42, 34, 1, '', '2025-01-07 03:16:55', 0, 1, NULL),
(113, 67, 34, 3, '', '2025-01-07 03:17:18', 0, 0, NULL),
(114, 48, 34, 2, '', '2025-01-07 03:17:41', 0, 0, NULL),
(115, 66, 34, 1, '', '2025-01-07 03:17:51', 0, 0, NULL),
(118, 70, 28, 2, 'moze być', '2025-01-10 01:22:53', 1, 0, '2025-01-16 23:01:24'),
(119, 39, 1, 1, 'gfdg666', '2025-01-10 01:27:00', 0, 0, '2025-01-10 02:27:11'),
(120, 48, 1, 1, 'dasd', '2025-01-15 23:21:04', 1, 0, NULL);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `review_votes`
--

CREATE TABLE `review_votes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `review_id` int(11) DEFAULT NULL,
  `vote` enum('like','dislike') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Dumping data for table `review_votes`
--

INSERT INTO `review_votes` (`id`, `user_id`, `review_id`, `vote`, `created_at`) VALUES
(75, 27, 61, 'like', '2025-01-07 03:07:03'),
(76, 27, 79, 'like', '2025-01-07 03:07:35'),
(77, 27, 89, 'like', '2025-01-07 03:07:36'),
(80, 27, 59, 'dislike', '2025-01-07 03:07:49'),
(81, 29, 102, 'like', '2025-01-07 03:09:39'),
(84, 29, 90, 'dislike', '2025-01-07 03:09:46'),
(85, 29, 80, 'like', '2025-01-07 03:09:48'),
(88, 29, 89, 'like', '2025-01-07 03:10:42'),
(89, 29, 79, 'like', '2025-01-07 03:10:43'),
(90, 29, 62, 'dislike', '2025-01-07 03:10:48'),
(91, 29, 91, 'like', '2025-01-07 03:10:48'),
(92, 29, 98, 'like', '2025-01-07 03:10:51'),
(93, 29, 82, 'like', '2025-01-07 03:10:52'),
(94, 29, 64, 'dislike', '2025-01-07 03:10:52'),
(95, 29, 95, 'like', '2025-01-07 03:11:02'),
(96, 29, 87, 'dislike', '2025-01-07 03:11:03'),
(97, 29, 63, 'like', '2025-01-07 03:11:03'),
(98, 29, 88, 'like', '2025-01-07 03:11:07'),
(99, 29, 58, 'like', '2025-01-07 03:11:14'),
(100, 29, 94, 'like', '2025-01-07 03:11:18'),
(101, 29, 59, 'like', '2025-01-07 03:11:18'),
(119, 32, 102, 'like', '2025-01-07 03:12:43'),
(120, 32, 88, 'dislike', '2025-01-07 03:12:43'),
(122, 32, 89, 'dislike', '2025-01-07 03:12:48'),
(123, 32, 79, 'dislike', '2025-01-07 03:12:48'),
(124, 32, 58, 'like', '2025-01-07 03:12:51'),
(125, 32, 94, 'dislike', '2025-01-07 03:12:54'),
(126, 32, 59, 'dislike', '2025-01-07 03:12:55'),
(128, 32, 71, 'like', '2025-01-07 03:13:28'),
(129, 32, 107, 'like', '2025-01-07 03:13:28'),
(130, 32, 105, 'dislike', '2025-01-07 03:13:40'),
(131, 32, 86, 'like', '2025-01-07 03:13:44'),
(132, 30, 102, 'dislike', '2025-01-07 03:14:29'),
(133, 30, 88, 'like', '2025-01-07 03:14:31'),
(135, 30, 89, 'dislike', '2025-01-07 03:14:35'),
(136, 30, 79, 'dislike', '2025-01-07 03:14:36'),
(137, 30, 58, 'like', '2025-01-07 03:14:39'),
(139, 30, 94, 'like', '2025-01-07 03:14:42'),
(140, 30, 59, 'dislike', '2025-01-07 03:14:43'),
(143, 30, 105, 'like', '2025-01-07 03:14:52'),
(144, 30, 86, 'like', '2025-01-07 03:14:52'),
(145, 30, 65, 'like', '2025-01-07 03:14:53'),
(151, 34, 58, 'like', '2025-01-07 03:16:13'),
(156, 34, 107, 'like', '2025-01-07 03:16:57'),
(157, 34, 112, 'dislike', '2025-01-07 03:16:57'),
(161, 34, 111, 'like', '2025-01-07 03:17:28'),
(165, 34, 88, 'like', '2025-01-07 03:20:40'),
(166, 34, 102, 'like', '2025-01-07 03:20:41'),
(168, 34, 89, 'like', '2025-01-07 03:21:02'),
(170, 34, 94, 'like', '2025-01-07 03:21:11'),
(172, 34, 59, 'like', '2025-01-07 03:21:12'),
(175, 34, 103, 'dislike', '2025-01-07 03:21:20'),
(176, 34, 90, 'dislike', '2025-01-07 03:21:21'),
(177, 34, 80, 'dislike', '2025-01-07 03:21:21'),
(179, 25, 79, 'dislike', '2025-01-07 03:23:18'),
(183, 25, 87, 'like', '2025-01-07 03:23:50'),
(184, 25, 82, 'like', '2025-01-07 03:24:00'),
(186, 28, 111, 'dislike', '2025-01-10 01:22:44'),
(188, 28, 102, 'like', '2025-01-10 01:22:47'),
(192, 34, 118, 'like', '2025-01-15 20:32:16'),
(194, 1, 120, 'like', '2025-01-15 23:21:09'),
(199, 1, 72, 'like', '2025-05-28 11:52:33');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `is_blocked` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`, `is_blocked`) VALUES
(1, 'admin', '$2y$10$ozOP7/y3bGrYplThY0AbvO1n5n1wFfPsTAa0J3OofCajb7P9nyWHe', 'admin', '2025-01-04 23:04:23', 0),
(2, 'user', '$2y$10$5oyebrE4c4dQkmE5uTPuoeTagcu6pSozSFGrb.XZM3BQD7Xl7nK3W', 'user', '2025-01-05 00:04:57', 0),
(25, 'Basia', '$2y$10$J9vi/Xo1S3wJek40UxDuOOVbHJ9Cx5HVG9ltJ9Ooee1S2FwhlEOdC', 'user', '2025-01-07 03:51:47', 1),
(26, 'Jan', '$2y$10$xz3DOYBv0cGKbsW2NBwY8uN8ksGblQ8bXpvtOpB.Ayrw5QdhzXcda', 'user', '2025-01-07 03:51:59', 0),
(27, 'Kasia', '$2y$10$o1UT4doUrtp1r0WKi0eESOUadfmcE4cK.tPgDQ3c/fygW1rjqTzHK', 'user', '2025-01-07 03:52:14', 0),
(28, 'Tomek', '$2y$10$/NzKSW0P6XvMJQvk4iK3qOBoFMnZeY5WNzrcWQZKjfHuvIX4Qw6VG', 'user', '2025-01-07 03:52:31', 0),
(29, 'Mateusz', '$2y$10$QjZr8NQUn8CZiRrDUYIc9OXQsAHNORbJ/q5zKGV2Y.1uw0XGakhqC', 'user', '2025-01-07 03:52:48', 0),
(30, 'Aleksander', '$2y$10$zXe5Y4kxSPYNO2yth75VMuJv7oViArWP7opmS7y5AafVBDCK54Io.', 'user', '2025-01-07 03:52:58', 0),
(32, 'Konrad', '$2y$10$mge4oXDzL.dLMzOw/CKfkuxnm.D1Q0eW998j8Z8JkNAAWtR7skXuC', 'user', '2025-01-07 03:53:36', 0),
(34, 'Gosia', '$2y$10$3mnPDVfq2Rqd4HkRMubsK.uUtzURvHRjfJ5NpBULPh7m/Ed8os6G2', 'user', '2025-01-07 03:55:20', 0),
(38, 'dasodnhasd', '$2y$10$Rfs.ylUAXqwvSzo/z2XWZOrEPMnkMQ6UWtzpkkiVp3YicGudZJvzK', 'user', '2025-01-16 00:20:09', 0),
(39, 'Martyna', '$2y$10$.u4RJkVIbmpt55HcfhOJX.5Oc1XIixeKFP2B6Pvml1ODHdVj.B4AW', 'user', '2025-01-19 02:37:00', 0),
(40, 'Michal', '$2y$10$4iYjXiVdtb2ZY6UqbuBOcu7Jb3xvViuIj/LuRXZ9Tnf1mKF5RoWYi', 'user', '2025-01-19 02:37:29', 0),
(41, 'tttttttyyy', '$2y$10$Ow/OPKB.eVYJqwmGNP5Nmuhc6cd0h.c3vxMRWbbn45ganZyFK6dMi', 'user', '2025-01-21 18:37:12', 0),
(42, 'asdasdsadasd', '$2y$10$4/1gI2zQY7LdA3NXJgwswOpeNo5MlDXLkcnQ2u6BRrs6QoEysHi4y', 'user', '2025-01-21 18:37:22', 0),
(43, 'asdsad', '$2y$10$NH4aJKHFG.KFjFtHgu/9o.ZDfT59qDLq8GcIutKon.kMH.YG6njCC', 'user', '2025-05-28 13:54:20', 0);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `user_activity_log`
--

CREATE TABLE `user_activity_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action_type` enum('add_review','edit_review','delete_review','like','dislike') NOT NULL,
  `film_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Dumping data for table `user_activity_log`
--

INSERT INTO `user_activity_log` (`id`, `user_id`, `action_type`, `film_id`, `created_at`) VALUES
(9, 28, 'delete_review', 67, '2025-01-21 14:09:02');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `films`
--
ALTER TABLE `films`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_category` (`category`);

--
-- Indeksy dla tabeli `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `film_id` (`film_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeksy dla tabeli `review_votes`
--
ALTER TABLE `review_votes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `review_id` (`review_id`);

--
-- Indeksy dla tabeli `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeksy dla tabeli `user_activity_log`
--
ALTER TABLE `user_activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `film_id` (`film_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `films`
--
ALTER TABLE `films`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=122;

--
-- AUTO_INCREMENT for table `review_votes`
--
ALTER TABLE `review_votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=200;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `user_activity_log`
--
ALTER TABLE `user_activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `films`
--
ALTER TABLE `films`
  ADD CONSTRAINT `fk_category` FOREIGN KEY (`category`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`film_id`) REFERENCES `films` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `review_votes`
--
ALTER TABLE `review_votes`
  ADD CONSTRAINT `review_votes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `review_votes_ibfk_2` FOREIGN KEY (`review_id`) REFERENCES `reviews` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_activity_log`
--
ALTER TABLE `user_activity_log`
  ADD CONSTRAINT `user_activity_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_activity_log_ibfk_2` FOREIGN KEY (`film_id`) REFERENCES `films` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
