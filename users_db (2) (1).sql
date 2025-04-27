-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 27, 2025 at 06:38 PM
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
-- Database: `users_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--

CREATE TABLE `addresses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `address_line1` varchar(255) DEFAULT NULL,
  `address_line2` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `type` enum('billing','shipping') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `addresses`
--

INSERT INTO `addresses` (`id`, `user_id`, `address_line1`, `address_line2`, `city`, `postal_code`, `country`, `type`) VALUES
(1, 30, 'B-07-05', 'PPR PINGGIRAN BUKIT JALIL', 'Kuala Lumpur', '58000', 'Malaysia', 'billing'),
(4, 31, 'B-07-05', 'PPR PINGGIRAN BUKIT JALIL', 'Kuala Lumpur', '58000', 'Malaysia', 'billing');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `size_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`user_id`, `product_id`, `size_id`, `quantity`, `category_id`) VALUES
(31, 12, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Men'),
(2, 'Women'),
(3, 'Kids');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `payment_id` varchar(255) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_status` enum('Paid','Canceled','Failed') DEFAULT 'Paid',
  `shipping_status` enum('Pending','Shipped','In Transit','Delivered') DEFAULT 'Pending',
  `address` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `payment_id`, `order_date`, `payment_status`, `shipping_status`, `address`) VALUES
(10, 30, 'pi_3RHSONQM571Me8gB0s6saUdu', '2025-04-24 16:11:05', 'Paid', 'Pending', 'B-07-05, PPR PINGGIRAN BUKIT JALIL, 58000 Kuala Lumpur, Malaysia'),
(11, 30, 'pi_3RHjH2QM571Me8gB102btBrK', '2025-04-25 10:12:37', 'Paid', 'Pending', 'B-07-05, PPR PINGGIRAN BUKIT JALIL, 58000 Kuala Lumpur, Malaysia'),
(12, 30, 'pi_3RHjIAQM571Me8gB0Lz9zr8r', '2025-04-25 10:13:48', 'Paid', 'Pending', 'B-07-05, PPR PINGGIRAN BUKIT JALIL, 58000 Kuala Lumpur, Malaysia'),
(13, 30, 'pi_3RHjKRQM571Me8gB0Pr1SDt6', '2025-04-25 10:16:08', 'Paid', 'Pending', 'B-07-05, PPR PINGGIRAN BUKIT JALIL, 58000 Kuala Lumpur, Malaysia'),
(14, 30, 'pi_3RHjNjQM571Me8gB0xjhUiX8', '2025-04-25 10:19:33', 'Paid', 'Pending', 'B-07-05, PPR PINGGIRAN BUKIT JALIL, 58000 Kuala Lumpur, Malaysia'),
(15, 30, 'pi_3RHjQMQM571Me8gB0eCrp7iq', '2025-04-25 10:22:16', 'Paid', 'Pending', 'B-07-05, PPR PINGGIRAN BUKIT JALIL, 58000 Kuala Lumpur, Malaysia'),
(16, 30, 'pi_3RHjViQM571Me8gB0IABin0e', '2025-04-25 10:27:48', 'Paid', 'Pending', 'B-07-05, PPR PINGGIRAN BUKIT JALIL, 58000 Kuala Lumpur, Malaysia'),
(17, 30, 'pi_3RHjXDQM571Me8gB0RuTuB2B', '2025-04-25 10:29:20', 'Paid', 'Pending', 'B-07-05, PPR PINGGIRAN BUKIT JALIL, 58000 Kuala Lumpur, Malaysia'),
(18, 30, 'pi_3RHjbsQM571Me8gB0jAquBss', '2025-04-25 10:34:10', 'Paid', 'Pending', 'B-07-05, PPR PINGGIRAN BUKIT JALIL, 58000 Kuala Lumpur, Malaysia'),
(20, 30, 'pi_3RHjgYQM571Me8gB0s8uzF2h', '2025-04-25 10:38:59', 'Paid', 'Pending', 'B-07-05, PPR PINGGIRAN BUKIT JALIL, 58000 Kuala Lumpur, Malaysia'),
(21, 30, 'pi_3RHjiOQM571Me8gB08lbVowV', '2025-04-25 10:40:53', 'Paid', 'Pending', 'B-07-05, PPR PINGGIRAN BUKIT JALIL, 58000 Kuala Lumpur, Malaysia'),
(22, 30, 'pi_3RHjkUQM571Me8gB0dGFxEy7', '2025-04-25 10:43:02', 'Paid', 'Pending', 'B-07-05, PPR PINGGIRAN BUKIT JALIL, 58000 Kuala Lumpur, Malaysia'),
(23, 30, 'pi_3RHjsPQM571Me8gB0tY72uA3', '2025-04-25 10:51:12', 'Paid', 'Pending', 'B-07-05, PPR PINGGIRAN BUKIT JALIL, 58000 Kuala Lumpur, Malaysia'),
(24, 30, 'pi_3RHZC0QM571Me8gB0K4YKzr2', '2025-04-25 10:57:41', 'Paid', 'Pending', 'B-07-05, PPR PINGGIRAN BUKIT JALIL, 58000 Kuala Lumpur, Malaysia'),
(25, 30, 'pi_3RHjzBQM571Me8gB0H7bWBCp', '2025-04-25 10:58:13', 'Paid', 'Delivered', 'B-07-05, PPR PINGGIRAN BUKIT JALIL, 58000 Kuala Lumpur, Malaysia'),
(29, 30, 'pi_3RI6RsQM571Me8gB0xyYhgZ1', '2025-04-26 11:00:35', 'Paid', 'Pending', 'B-07-05, PPR PINGGIRAN BUKIT JALIL, 58000 Kuala Lumpur, Malaysia'),
(30, 30, 'pi_3RI6RsQM571Me8gB0xyYhgZ1', '2025-04-26 11:00:41', 'Paid', 'Pending', 'B-07-05, PPR PINGGIRAN BUKIT JALIL, 58000 Kuala Lumpur, Malaysia'),
(31, 30, 'pi_3RI6RsQM571Me8gB0xyYhgZ1', '2025-04-26 11:00:45', 'Paid', 'Pending', 'B-07-05, PPR PINGGIRAN BUKIT JALIL, 58000 Kuala Lumpur, Malaysia'),
(32, 30, 'pi_3RI6VjQM571Me8gB0Te0Mqdx', '2025-04-26 11:03:14', 'Paid', 'Shipped', 'B-07-05, PPR PINGGIRAN BUKIT JALIL, 58000 Kuala Lumpur, Malaysia'),
(33, 30, 'pi_3RISiNQM571Me8gB0d7cEg1l', '2025-04-27 10:45:51', 'Paid', 'Pending', 'B-07-05, PPR PINGGIRAN BUKIT JALIL, 58000 Kuala Lumpur, Malaysia');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `size_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `sold_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `image`, `sold_count`) VALUES
(10, 'NIke Air Zoom Alphafly Next 3 Premium', 'Fine-tuned for speed, the Alphafly 3 Premium helps push you beyond what you thought possible. Three innovative technologies power your run: a double dose of Air Zoom units helps launch you into your next step; a full-length carbon-fibre plate helps propel you forwards with ease; and a heel-to-toe ZoomX foam midsole helps keep you fresh as you conquer the iconic ekiden.', 1259.00, 'AIR+ZOOM+ALPHAFLY+NEXT%+3+PRM.png', 0),
(11, 'LeBron XXII EP', 'LeBron isn\'t slowing down any time soon. The open-court nightmare is as fast and spry as ever. But even Bron needs support when he\'s at full throttle. That\'s why we levelled up the LeBron 22. Newly implemented saddle wings offer optimal midfoot stability. They complement the heel and forefoot Air Zoom units, so the King can keep pushing the sport forwards. With its extra-durable rubber outsole, this version gives you traction for outdoor courts.', 939.00, 'LEBRON+XXII+BW+EP.avif', 0),
(12, 'JA 2 EP', 'Ja puts in the work to be great so he can be at his best when the game\'s on the line. That\'s why we gave the Ja 2 a lighter design. It works with Air Zoom cushioning to help keep him fresh when he\'s in the lab. And the tractor tyre-inspired traction helps him change direction with ease. Ja\'s about that grind. Are you? With its extra-durable rubber outsole, this version gives you traction for outdoor courts.', 609.00, 'JA+2+EP(2).png', 0),
(13, 'Book 1 EP', 'Devin Booker is a craftsman who can lose a defender with an ankle-snatching \"stutter/go\", then come back with a series of spellbinding jabs into a splashed jumper. Book\'s signature shoe gives him the tools he needs to carve. With leather accents highlighting a super-smooth upper and a speedy, cushioned ride, this design can help you explore the spaces created by your footwork and hungry-hooper soul. With its extra-durable rubber outsole, this version gives you traction for outdoor courts.', 639.00, 'BOOK+1+EP (2).png', 0),
(14, 'Sabrina 2', 'Sabrina Ionescu’s success is no secret. Her game is based on living in the gym, getting in rep after rep to perfect her craft. The Sabrina 2 sets you up to do more, so you\'re ready to go when it\'s game-time. Our newest Cushlon foam helps keep you fresh, Air Zoom cushioning adds the pop, and sticky traction helps you create that next-level distance. Sabrina’s handed you the tools. Time to go to work.', 568.00, 'SABRINA+2+EP (2).png', 0),
(15, 'NIke Calm', 'Enjoy a calm, comfortable experience—no matter where your day off takes you. Made with soft yet supportive foam, the minimal design makes these slides easy to style with or without socks. And they’ve got a textured footbed to help keep your feet in place.', 199.00, 'NIKE+CALM+SLIDE.png', 0),
(16, 'Jordan Post', 'Quick, comfy, cool. Made from robust and flexible foam, these slides are designed to stay secure as you rack up those steps. A wide foot covering holds your foot in place, while an asymmetrical design gives you a distinct look.', 139.00, 'WMNS+JORDAN+POST+SLIDE.png', 0),
(17, 'Nike V2K Run', 'Fast-forward. Rewind. Doesn\'t matter—this shoe takes retro into the future. The V2K remasters everything you love about the Vomero in a look pulled straight from an early \'00s running catalogue. Layer up in a mixture of flashy metallics, referential plastic details and a midsole with a perfectly vintage aesthetic. And the chunky heel makes sure wherever you go, it\'s in comfort.', 539.00, '680e3287dfb5a_NIKE+V2K+RUN (3).png', 0),
(19, 'Nike Dunk Low Retro', 'Created for the hardwood but taken to the streets, the Nike Dunk Low Retro returns with crisp overlays and original team colours. This basketball icon channels \'80s vibes with premium leather in the upper that looks good and breaks in even better. Modern footwear technology helps bring the comfort into the 21st century.', 489.00, 'NIKE+DUNK+LOW+RETRO (2).png', 0),
(20, 'Air Jordan 4 Retro', 'Step into a classic. This AJ4 throws it back with full-grain and synthetic leathers and premium textiles. Lush colours update the icon, while original design elements—like floating eyestays and mesh-inspired accents—feel just as fresh as they did in \'89.', 896.00, 'AIR+JORDAN+4+RETRO (2).png', 0),
(23, 'Nike Air Max 97', 'The AM97 was the shapeshifter of its time, and it\'s your turn to do the same. Customise every part of the shoe from upper materials to the colours of the midsole and Nike Air unit, plus non-slip laces to secure the fit. Then, decide whether you want your outsole solid, tinted or translucent. There\'s even an upgraded insole for extra cushioning underfoot. Finally, a shoe as multifaceted as you.', 869.00, 'custom-nike-air-max-97-shoes-by-you.avif', 0),
(24, 'Air Jordan 1 Retro High OG \'Black Toe\'', 'The Air Jordan 1 Retro High remakes the classic sneaker, giving you a fresh look with a familiar feel. Premium materials with new colours and textures give modern expression to an all-time favourite.', 678.00, 'air-jordan-1-high-og-black-toe-dz5485-106-release-date (2).jpg', 0),
(26, 'Nike Air Force 1 \'07', 'Comfortable, durable and timeless—it\'s number one for a reason. The classic \'80s construction pairs smooth leather with bold details for style that tracks whether you\'re on court or on the go.', 489.00, 'AIR+FORCE+1+\'07 (2).png', 0),
(29, 'Nike Downshifter 13', 'Whether you\'re starting your running journey or an expert eager to switch up your pace, the Downshifter 13 is down for the ride. With a revamped upper, cushioning and durability, it helps you find that extra gear or take that first stride towards chasing down your goals.', 255.00, 'NIKE+DOWNSHIFTER+13 (3).png', 0),
(30, 'Nike Pegasus 41', 'Responsive cushioning in the Pegasus provides an energised ride for everyday road running. Experience lighter-weight energy return with dual Air Zoom units and a ReactX foam midsole. Improved engineered mesh on the upper decreases weight and increases breathability.', 659.00, 'AIR+ZOOM+PEGASUS+41 (2).png', 0),
(31, 'Nike Air Max Dn', 'Say hello to the next generation of Air technology. The Air Max Dn features our Dynamic Air unit system of dual-pressure tubes, creating a bouncy sensation with every step. This results in a futuristic design that\'s comfortable enough to wear all day. Go ahead—Feel The Unreal.\r\n\r\n', 489.00, 'Nike Air Max Dn.png', 0),
(32, 'Nike Court Borough Low Recraft', 'Start little ones off on the right foot with the new Court Borough. Made for the long haul, this \"recrafted\" legend uses a combination of durable materials on the upper and outsole to achieve a classic look made in a whole new way. A redesigned toe box and midfoot give feet a little extra room so little ones can run, jump and play just a bit longer and harder in comfort.\r\n\r\n', 285.00, 'Nike Court Borough Low Recraft.png', 0),
(33, 'Nike Dunk Low', 'The Nike Dunk Low is an easy score for your kid\'s wardrobe. This mid-80s hoops icon returns with super-durable construction and original colours. Add in a rubber sole for traction and this one\'s a slam dunk for young sneakerheads.', 245.00, 'Nike Dunk Low.png', 0),
(34, 'Nike IsoFly', 'Lace up the Nike IsoFly to help your little one feel lightweight and bouncy as they play. With durable traction, it provides sticky grip in the playground and inside on a hardwood court. The extra cushioning underfoot and breathable material up top help kids run and jump in comfort. Plus, a hook-and-loop strap and elastic laces help make getting them on an easy win.\r\n\r\n', 319.00, 'Nike IsoFly.png', 0),
(35, 'Nike Mercurial Superfly 10 Elite', 'Obsessed with speed? So are the game\'s biggest stars. That\'s why we made this Elite boot with an improved 3/4-length Air Zoom unit. It gives you and the sport\'s fastest players the propulsive feel needed to break through the back line. The result is the most responsive Mercurial we\'ve ever made, because you demand greatness from yourself and your footwear.\r\n\r\n', 1299.00, 'Nike Mercurial Superfly 10 Elite.png', 0),
(36, 'Nike Phantom Luna 2 Elite', 'Obsessed with perfecting your craft? We made this for you. In the middle of the storm, with chaos swirling all around you, you\'ve calmly found the final third of the pitch, thanks to your uncanny mix of on-ball guile and grace. Go and finish the job in the Phantom Luna 2 Elite. Revolutionary Nike Gripknit covers the striking area of the boot while Nike Cyclone 360 traction helps guide your unscripted agility. We design Elite boots for you and the world\'s biggest stars to give you high-level quality, because you demand greatness from yourself and your footwear.', 1299.00, 'Nike Phantom Luna 2 Elite.png', 0),
(37, 'Nike Tiempo Legend 10 Academy', 'Even Legends find ways to evolve. Made to take your game to the next level, the latest iteration of this Academy boot has all-new FlyTouch Lite engineered leather. Softer than natural leather, it contours to your foot and doesn\'t overstretch, so you can dictate the pace of your game. Lighter and sleeker than any other Tiempo to date, the Legend 10 is for any position on the pitch, whether you\'re sending a pinpoint pass through the defence or tracking back to stop a break-away.', 455.00, 'Nike Tiempo Legend 10 Academy.png', 0),
(38, 'Nike Phantom GX 2 Club', 'Whether you\'re starting out or just playing for fun, the Club boot gets you on the pitch without compromising on quality. Created with goals in mind, the Phantom GX 2 Club has a grippy texture covering the striking area of the boot and reliable traction to help guide your unscripted agility.\r\n\r\n', 245.00, 'Nike Phantom GX 2 Club.png', 0),
(39, 'Nike Phantom GX 2 Elite \'Erling Haaland\'', 'Erling Haaland is inevitable. There\'s no stopping the goal-scoring force of nature with the game on the line. That\'s why he needs the Phantom GX 2 Elite. Its sticky Gripknit technology gives him the precision he needs to find the back of the net. With graphics inspired by natural phenomena like supernovas and meteor crashes, this special version speaks to the unpredictable force Erling is on the pitch.', 1199.00, 'Nike Phantom GX 2 Elite \'Erling Haaland\'.png', 0),
(40, 'Nike Zoom Fly 6', 'The Zoom Fly 6 is with you every step of your run. Lighter than the Zoom Fly 5, its responsive ZoomX foam adds energy return to each stride, while a carbon-fibre plate helps propel you to the finishing line.', 789.00, 'Nike Zoom Fly 6.png', 0),
(41, 'Nike Air Max INTRLK Lite', 'You\'ve got to feel the sensation to be the sensation. Enter the Air Max INTRLK. Rethought cushioning turns the page in comfort with increased responsiveness and the perfect amount of bounce. Lightweight, easy-to-style materials stand up to wear and tear. Finishing it off, the Waffle-inspired outsole adds true Nike zest.\r\n\r\n', 355.00, 'Nike Air Max INTRLK Lite.png', 0),
(42, 'Nike Cortez Textile', 'One word: tradition. From heritage running to a fashion phenom, the Cortez\'s retro appeal, sponge-soft midsole and seesaw detailing deliver decade after decade. This iteration combines ribbon lacing and a monochromatic look for elevated style.', 469.00, 'Nike Cortez Textile.png', 0),
(43, 'Nike Gamma Force', 'Layers upon layers of dimensional style—that\'s a force to be reckoned with. Offering both comfort and versatility, these kicks are rooted in heritage basketball culture. Collar materials pay homage to vintage sport while the subtle platform elevates your look, literally. The Gamma Force is forging its own legacy: court style that can be worn all day, wherever you go.\r\n\r\n', 395.00, 'Nike Gamma Force.png', 0),
(44, 'Nike Victori One', 'From the beach to gardening to sitting on the sofa, the Nike Victori One perfects a classic design. Delivering lightweight comfort that\'s easy to wear, it features new softer, more responsive foam. The contoured grip pattern helps cradle and hold your foot in place.\r\n\r\n', 129.00, 'Nike Victori One.png', 0),
(46, 'Nike SB Dunk Low Pro', 'An \'80s b-ball icon returns with classic details and throwback hoops flair. Channelling vintage style back onto the streets, the Nike SB Dunk features a padded low-cut collar that lets you comfortably take your game anywhere.\r\n\r\n', 489.00, 'Nike SB Dunk Low Pro.png', 0),
(47, 'Nike ReactX Rejuven8', 'Give your feet a rest. Made from soft and responsive ReactX foam, the Rejuven8 uses some of our best tech to create a comfortable shoe you\'ll want to slip on every day.\r\n\r\n', 289.00, 'Nike ReactX Rejuven8.png', 0);

-- --------------------------------------------------------

--
-- Table structure for table `product_categories`
--

CREATE TABLE `product_categories` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_categories`
--

INSERT INTO `product_categories` (`id`, `product_id`, `category_id`) VALUES
(26, 11, 1),
(28, 12, 1),
(30, 13, 1),
(31, 14, 1),
(32, 14, 2),
(33, 15, 1),
(34, 15, 2),
(35, 10, 1),
(36, 16, 1),
(37, 16, 2),
(40, 17, 1),
(43, 19, 1),
(44, 19, 2),
(45, 20, 1),
(46, 20, 2),
(47, 20, 3),
(52, 23, 1),
(53, 23, 2),
(54, 24, 1),
(55, 24, 2),
(56, 24, 3),
(62, 26, 1),
(63, 26, 2),
(68, 29, 1),
(69, 29, 2),
(70, 30, 1),
(71, 30, 2),
(72, 31, 3),
(73, 32, 3),
(74, 33, 3),
(75, 34, 3),
(76, 35, 1),
(77, 35, 3),
(78, 36, 1),
(79, 37, 1),
(80, 38, 1),
(81, 39, 1),
(82, 39, 3),
(83, 40, 2),
(84, 41, 2),
(85, 42, 2),
(86, 43, 2),
(87, 44, 2),
(89, 46, 1),
(90, 46, 3),
(91, 47, 3);

-- --------------------------------------------------------

--
-- Table structure for table `product_sizes`
--

CREATE TABLE `product_sizes` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `size_id` int(11) NOT NULL,
  `stock` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_sizes`
--

INSERT INTO `product_sizes` (`id`, `product_id`, `size_id`, `stock`) VALUES
(24, 10, 1, 20),
(25, 10, 2, 20),
(26, 10, 3, 20),
(27, 10, 4, 20),
(28, 10, 5, 20),
(29, 10, 6, 20),
(30, 10, 7, 20),
(31, 10, 8, 20),
(32, 10, 9, 20),
(33, 10, 10, 20),
(34, 10, 11, 20),
(35, 10, 12, 20),
(36, 11, 1, 54),
(37, 11, 2, 123),
(38, 11, 3, 21),
(39, 11, 4, 45),
(40, 11, 5, 68),
(41, 11, 6, 23),
(42, 11, 7, 53),
(43, 11, 8, 12),
(44, 11, 9, 35),
(45, 11, 10, 34),
(46, 11, 11, 78),
(47, 11, 12, 64),
(48, 12, 1, 34),
(49, 12, 2, 51),
(50, 12, 3, 23),
(51, 12, 4, 74),
(52, 12, 5, 64),
(53, 12, 6, 43),
(54, 12, 7, 34),
(55, 12, 8, 68),
(56, 12, 9, 74),
(57, 12, 10, 34),
(58, 12, 11, 67),
(59, 12, 12, 83),
(60, 13, 1, 34),
(61, 13, 2, 65),
(62, 13, 3, 83),
(63, 13, 4, 23),
(64, 13, 5, 57),
(65, 13, 6, 57),
(66, 13, 7, 23),
(67, 13, 8, 57),
(68, 13, 9, 97),
(69, 13, 10, 54),
(70, 13, 11, 35),
(71, 13, 12, 14),
(72, 14, 1, 34),
(73, 14, 2, 57),
(74, 14, 3, 80),
(75, 14, 4, 85),
(76, 14, 5, 34),
(77, 14, 6, 79),
(78, 14, 7, 63),
(79, 14, 8, 42),
(80, 14, 9, 12),
(81, 14, 10, 57),
(82, 14, 11, 90),
(83, 14, 12, 75),
(84, 14, 13, 34),
(85, 14, 14, 75),
(86, 14, 15, 42),
(87, 14, 16, 53),
(88, 14, 17, 21),
(89, 14, 18, 53),
(90, 14, 19, 75),
(91, 14, 20, 34),
(92, 14, 21, 74),
(93, 14, 22, 12),
(94, 14, 23, 86),
(95, 15, 1, 23),
(96, 15, 2, 62),
(97, 15, 3, 35),
(98, 15, 4, 53),
(99, 15, 5, 12),
(100, 15, 6, 78),
(101, 15, 7, 97),
(102, 15, 8, 43),
(103, 15, 9, 43),
(104, 15, 10, 12),
(105, 15, 11, 43),
(106, 15, 12, 76),
(107, 15, 13, 34),
(108, 15, 14, 75),
(109, 15, 15, 12),
(110, 15, 16, 78),
(111, 15, 17, 64),
(112, 15, 18, 21),
(113, 15, 19, 35),
(114, 15, 20, 79),
(115, 15, 21, 87),
(116, 15, 22, 65),
(117, 15, 23, 23),
(118, 16, 1, 32),
(119, 16, 2, 53),
(120, 16, 4, 23),
(121, 16, 6, 52),
(122, 16, 8, 55),
(123, 16, 10, 43),
(124, 16, 12, 21),
(125, 16, 13, 23),
(126, 16, 15, 64),
(127, 16, 16, 34),
(128, 16, 17, 65),
(129, 16, 19, 10),
(130, 16, 22, 23),
(131, 17, 1, 13),
(132, 17, 2, 23),
(133, 17, 4, 42),
(134, 17, 6, 32),
(135, 17, 7, 53),
(136, 17, 8, 42),
(137, 17, 10, 12),
(138, 17, 12, 32),
(139, 17, 13, 42),
(140, 17, 14, 12),
(141, 17, 16, 23),
(142, 17, 19, 46),
(143, 17, 21, 86),
(144, 17, 22, 23),
(160, 19, 1, 42),
(161, 19, 3, 32),
(162, 19, 5, 30),
(163, 19, 7, 52),
(164, 19, 9, 43),
(165, 19, 11, 43),
(166, 19, 12, 31),
(167, 19, 13, 42),
(168, 19, 14, 42),
(169, 19, 16, 53),
(170, 19, 18, 23),
(171, 19, 20, 64),
(172, 19, 22, 64),
(173, 19, 23, 75),
(174, 20, 1, 42),
(175, 20, 3, 43),
(176, 20, 4, 53),
(177, 20, 6, 67),
(178, 20, 9, 78),
(179, 20, 10, 42),
(180, 20, 12, 12),
(181, 20, 13, 42),
(182, 20, 14, 53),
(183, 20, 15, 53),
(184, 20, 18, 23),
(185, 20, 19, 23),
(186, 20, 21, 43),
(187, 20, 23, 13),
(188, 20, 25, 42),
(189, 20, 27, 53),
(190, 20, 29, 65),
(191, 20, 30, 42),
(220, 23, 1, 12),
(221, 23, 2, 43),
(222, 23, 3, 43),
(223, 23, 4, 23),
(224, 23, 7, 54),
(225, 23, 8, 78),
(226, 23, 9, 98),
(227, 23, 11, 42),
(228, 23, 13, 23),
(229, 23, 15, 42),
(230, 23, 16, 41),
(231, 23, 18, 31),
(232, 23, 20, 12),
(233, 23, 21, 42),
(234, 23, 23, 32),
(235, 24, 1, 23),
(236, 24, 2, 31),
(237, 24, 3, 45),
(238, 24, 5, 43),
(239, 24, 7, 42),
(240, 24, 8, 63),
(241, 24, 10, 32),
(242, 24, 12, 41),
(243, 24, 13, 12),
(244, 24, 15, 32),
(245, 24, 16, 56),
(246, 24, 18, 32),
(247, 24, 19, 23),
(248, 24, 21, 53),
(249, 24, 22, 64),
(250, 24, 24, 32),
(251, 24, 25, 42),
(252, 24, 26, 32),
(253, 24, 28, 45),
(254, 24, 30, 12),
(275, 26, 1, 42),
(276, 26, 2, 32),
(277, 26, 3, 23),
(278, 26, 4, 32),
(279, 26, 6, 12),
(280, 26, 9, 43),
(281, 26, 11, 53),
(282, 26, 13, 12),
(283, 26, 14, 32),
(284, 26, 16, 54),
(285, 26, 18, 42),
(286, 26, 20, 43),
(287, 26, 21, 53),
(308, 29, 1, 25),
(309, 29, 2, 32),
(310, 29, 5, 44),
(311, 29, 6, 42),
(312, 29, 9, 64),
(313, 29, 11, 23),
(314, 29, 13, 42),
(315, 29, 16, 32),
(316, 29, 17, 53),
(317, 29, 18, 53),
(318, 29, 19, 21),
(319, 29, 20, 42),
(320, 29, 23, 12),
(321, 30, 1, 45),
(322, 30, 4, 32),
(323, 30, 5, 23),
(324, 30, 6, 23),
(325, 30, 7, 34),
(326, 30, 10, 56),
(327, 30, 12, 12),
(328, 30, 13, 62),
(329, 30, 15, 53),
(330, 30, 17, 43),
(331, 30, 18, 23),
(332, 30, 19, 24),
(333, 30, 20, 643),
(334, 30, 23, 26),
(335, 31, 24, 12),
(336, 31, 25, 14),
(337, 31, 26, 12),
(338, 31, 27, 13),
(339, 31, 28, 12),
(340, 31, 29, 15),
(341, 31, 30, 16),
(342, 31, 31, 11),
(343, 32, 24, 11),
(344, 32, 25, 0),
(345, 32, 26, 0),
(346, 32, 27, 12),
(347, 32, 28, 0),
(348, 32, 29, 0),
(349, 32, 30, 0),
(350, 32, 31, 5),
(351, 33, 24, 12),
(352, 33, 25, 12),
(353, 33, 26, 0),
(354, 33, 27, 11),
(355, 33, 28, 6),
(356, 33, 29, 15),
(357, 33, 30, 16),
(358, 33, 31, 11),
(359, 34, 24, 12),
(360, 34, 25, 14),
(361, 34, 26, 15),
(362, 34, 27, 12),
(363, 34, 28, 11),
(364, 34, 29, 10),
(365, 34, 30, 0),
(366, 34, 31, 2),
(367, 35, 1, 12),
(368, 35, 2, 11),
(369, 35, 3, 19),
(370, 35, 4, 15),
(371, 35, 5, 16),
(372, 35, 6, 12),
(373, 35, 7, 10),
(374, 35, 8, 11),
(375, 35, 9, 11),
(376, 35, 10, 14),
(377, 35, 11, 6),
(378, 35, 12, 7),
(379, 35, 24, 11),
(380, 35, 25, 12),
(381, 35, 26, 16),
(382, 35, 27, 17),
(383, 35, 28, 12),
(384, 35, 29, 19),
(385, 35, 30, 2),
(386, 35, 31, 10),
(387, 36, 1, 12),
(388, 36, 2, 11),
(389, 36, 3, 17),
(390, 36, 4, 16),
(391, 36, 5, 10),
(392, 36, 6, 9),
(393, 36, 7, 12),
(394, 36, 8, 15),
(395, 36, 9, 12),
(396, 36, 10, 13),
(397, 36, 11, 16),
(398, 36, 12, 10),
(399, 37, 1, 0),
(400, 37, 2, 0),
(401, 37, 3, 0),
(402, 37, 4, 0),
(403, 37, 5, 12),
(404, 37, 6, 15),
(405, 37, 7, 16),
(406, 37, 8, 12),
(407, 37, 9, 22),
(408, 37, 10, 16),
(409, 37, 11, 17),
(410, 37, 12, 6),
(411, 38, 1, 0),
(412, 38, 2, 0),
(413, 38, 3, 12),
(414, 38, 4, 0),
(415, 38, 5, 15),
(416, 38, 6, 16),
(417, 38, 7, 21),
(418, 38, 8, 16),
(419, 38, 9, 17),
(420, 38, 10, 0),
(421, 38, 11, 1),
(422, 38, 12, 24),
(423, 39, 1, 12),
(424, 39, 2, 11),
(425, 39, 3, 11),
(426, 39, 4, 17),
(427, 39, 5, 16),
(428, 39, 6, 18),
(429, 39, 7, 14),
(430, 39, 8, 15),
(431, 39, 9, 8),
(432, 39, 10, 21),
(433, 39, 11, 7),
(434, 39, 12, 10),
(435, 39, 24, 4),
(436, 39, 25, 4),
(437, 39, 26, 5),
(438, 39, 27, 17),
(439, 39, 28, 15),
(440, 39, 29, 7),
(441, 39, 30, 1),
(442, 39, 31, 12),
(443, 40, 13, 5),
(444, 40, 14, 0),
(445, 40, 15, 15),
(446, 40, 16, 12),
(447, 40, 17, 19),
(448, 40, 18, 6),
(449, 40, 19, 10),
(450, 40, 20, 12),
(451, 40, 21, 0),
(452, 40, 22, 11),
(453, 40, 23, 11),
(454, 41, 13, 15),
(455, 41, 14, 12),
(456, 41, 15, 10),
(457, 41, 16, 11),
(458, 41, 17, 19),
(459, 41, 18, 15),
(460, 41, 19, 22),
(461, 41, 20, 16),
(462, 41, 21, 12),
(463, 41, 22, 9),
(464, 41, 23, 12),
(465, 42, 13, 12),
(466, 42, 14, 15),
(467, 42, 15, 0),
(468, 42, 16, 12),
(469, 42, 17, 0),
(470, 42, 18, 18),
(471, 42, 19, 12),
(472, 42, 20, 19),
(473, 42, 21, 11),
(474, 42, 22, 8),
(475, 42, 23, 10),
(476, 43, 13, 0),
(477, 43, 14, 12),
(478, 43, 15, 13),
(479, 43, 16, 15),
(480, 43, 17, 11),
(481, 43, 18, 9),
(482, 43, 19, 12),
(483, 43, 20, 18),
(484, 43, 21, 19),
(485, 43, 22, 15),
(486, 43, 23, 16),
(487, 44, 13, 2),
(488, 44, 14, 11),
(489, 44, 15, 10),
(490, 44, 16, 19),
(491, 44, 17, 18),
(492, 44, 18, 17),
(493, 44, 19, 16),
(494, 44, 20, 15),
(495, 44, 21, 14),
(496, 44, 22, 9),
(497, 44, 23, 16),
(509, 46, 1, 11),
(510, 46, 2, 10),
(511, 46, 3, 9),
(512, 46, 4, 8),
(513, 46, 5, 12),
(514, 46, 6, 12),
(515, 46, 7, 21),
(516, 46, 8, 19),
(517, 46, 9, 18),
(518, 46, 10, 15),
(519, 46, 11, 10),
(520, 46, 12, 12),
(521, 46, 24, 19),
(522, 46, 25, 18),
(523, 46, 26, 10),
(524, 46, 27, 12),
(525, 46, 28, 0),
(526, 46, 29, 0),
(527, 46, 30, 12),
(528, 46, 31, 8),
(529, 47, 24, 0),
(530, 47, 25, 0),
(531, 47, 26, 0),
(532, 47, 27, 0),
(533, 47, 28, 8),
(534, 47, 29, 12),
(535, 47, 30, 9),
(536, 47, 31, 12);

-- --------------------------------------------------------

--
-- Table structure for table `sizes`
--

CREATE TABLE `sizes` (
  `id` int(11) NOT NULL,
  `size_label` varchar(255) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sizes`
--

INSERT INTO `sizes` (`id`, `size_label`, `category_id`) VALUES
(1, 'UK 6 (EU 40)', 1),
(2, 'UK 6.5', 1),
(3, 'UK 7', 1),
(4, 'UK 7.5', 1),
(5, 'UK 8', 1),
(6, 'UK 8.5', 1),
(7, 'UK 9', 1),
(8, 'UK 9.5', 1),
(9, 'UK 10', 1),
(10, 'UK 10.5', 1),
(11, 'UK 11', 1),
(12, 'UK 12', 1),
(13, 'UK 2.5', 2),
(14, 'UK 3', 2),
(15, 'UK 3.5', 2),
(16, 'UK 4', 2),
(17, 'UK 4.5', 2),
(18, 'UK 5', 2),
(19, 'UK 5.5', 2),
(20, 'UK 6', 2),
(21, 'UK 6.5', 2),
(22, 'UK 7', 2),
(23, 'UK 7.5', 2),
(24, 'UK 3', 3),
(25, 'UK 3.5', 3),
(26, 'UK 4', 3),
(27, 'UK 4.5', 3),
(28, 'UK 5', 3),
(29, 'UK 5.5', 3),
(30, 'UK 6 (EU 39)', 3),
(31, 'UK 6 (EU 40)', 3);

-- --------------------------------------------------------

--
-- Table structure for table `tokens`
--

CREATE TABLE `tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `token_type` enum('reset') NOT NULL,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tokens`
--

INSERT INTO `tokens` (`id`, `user_id`, `token`, `token_type`, `expires_at`) VALUES
(13, 30, 'e8a05dd98a2ece54d7c03778b37c37049034ade1', 'reset', '2025-04-20 09:30:59'),
(14, 30, '781fb289424c3754545a64bad26d6e68fbe119d9', 'reset', '2025-04-20 09:51:56'),
(15, 30, '25ac3bba5cc391ba361c193414699ba66b8b1add', 'reset', '2025-04-20 09:55:07'),
(17, 30, 'b9fa248a5e9ad7bed54c8a962fb3826c87948994', 'reset', '2025-04-20 09:55:32'),
(18, 30, '940fd39598126bd550fec064bd92ec0b61510ae8', 'reset', '2025-04-20 10:00:56'),
(19, 30, 'd3d25135fc6ca462158f5135f37334200b77429c', 'reset', '2025-04-20 10:01:01'),
(20, 30, '9bdbbc5b185f9f6b7552403e7d354b76b5f86b4e', 'reset', '2025-04-20 10:02:27'),
(21, 30, '954d40d2f90fdf596f5c135256a03e1ce54d47c9', '', '2025-04-20 10:06:57'),
(22, 30, '60fc3b01023d6544610cc966a5bd4579c09989a7', 'reset', '2025-04-20 10:10:08'),
(23, 30, '37602c1c7cd704d49e76e76128a4a270197cdea1', '', '2025-04-20 12:48:06'),
(24, 30, '582c16696e38951af08ed71e887a0097b85ecdad', 'reset', '2025-04-20 12:49:45'),
(25, 30, '5314e4729d1254c95bd4405ba0207de62749bfe2', 'reset', '2025-04-22 15:39:54'),
(26, 30, 'a16922727fc45744156634b7895a5cdb436a7a25', 'reset', '2025-04-26 14:02:33'),
(27, 30, '078da61b0525e927acfa671d146716aae687dd3c', 'reset', '2025-04-26 14:02:37'),
(28, 30, '6c3313121faa86535dc56f2d38f08736e68823bb', 'reset', '2025-04-26 14:02:40'),
(29, 30, 'f65f22a34098d585bd09e3cd6e3a1fa4f3dd976c', 'reset', '2025-04-26 14:02:43'),
(30, 30, '7f322a59d285456752255a20653e72a02aa6cb97', 'reset', '2025-04-26 14:02:47'),
(31, 30, 'beee0dd38e6030cacf1437e4a8fa74a37a540ef7', 'reset', '2025-04-26 14:02:49'),
(32, 30, '475dbe4f1cb087aec03620db699389e3b3bc4d08', 'reset', '2025-04-26 14:02:52'),
(33, 30, 'c05ffbecca81b3222440df0f2c884394b437cbad', 'reset', '2025-04-26 14:02:55'),
(34, 30, '4b318338cb7da483f949f7062eb9a778f5c681a5', 'reset', '2025-04-26 14:02:58'),
(35, 30, '24bf9a13c617ce2dc8af4b7ceb3c44ed3f2b97c3', 'reset', '2025-04-26 14:03:01'),
(36, 30, '9d906f53566b5c304c863250f172a578ae5000ee', 'reset', '2025-04-26 14:03:04'),
(37, 30, '8db2ff7a9b8d3ec8717c440493eb813759d75f2a', 'reset', '2025-04-26 14:03:07'),
(38, 30, '3b0d65ce1bf012e3699a71fc3c42b8897a3d3c25', 'reset', '2025-04-26 14:03:10'),
(39, 30, 'f06244723d051428c09d2c13160348bf946070e2', 'reset', '2025-04-26 14:03:13'),
(40, 30, '53f6a38be3918a5d6d2ae383bb8d84e168ccee30', 'reset', '2025-04-26 14:03:15'),
(41, 30, '515f873a6ebb6ccb3ed6c44f7b4cea9b2c9ba946', 'reset', '2025-04-26 14:03:18'),
(42, 30, 'c8104b0192d501d2033334edb0a0bde32ad6ca0e', 'reset', '2025-04-26 14:03:21'),
(43, 30, 'e9c8e069e6c6d74d08fc80a60a3cf855d4bfceed', 'reset', '2025-04-26 14:03:45'),
(45, 30, '7e45eb324066fcdf90c73a6f2a0ee444ce2a8730', 'reset', '2025-04-27 12:16:37'),
(46, 30, 'dde8193547a199a681cc2c2d249f1a04eb24e9c2', 'reset', '2025-04-27 12:18:36');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_admin` tinyint(1) DEFAULT 0,
  `profile_picture` varchar(100) DEFAULT NULL,
  `failed_attempts` int(11) DEFAULT 0,
  `last_attempt_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`, `is_admin`, `profile_picture`, `failed_attempts`, `last_attempt_time`) VALUES
(30, 'x0uls', 'kennykoh20061027@gmail.com', '8ee396cfbac4eaf092c01855c35fdb9675535551', '2025-04-20 05:52:05', 0, '6809b9a422cbc.png', 0, NULL),
(31, 'admin1', 'admin@gmail.com', '9d25cff3b5eb849da42581b8011128d44027d07e', '2025-04-21 02:25:36', 1, '680cb92c5d1a0.png', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `size_id` int(11) DEFAULT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`user_id`,`product_id`,`size_id`),
  ADD UNIQUE KEY `unique_cart_combo` (`user_id`,`product_id`,`size_id`,`category_id`),
  ADD KEY `fk_cart_product` (`product_id`),
  ADD KEY `fk_cart_size` (`size_id`),
  ADD KEY `fk_cart_category` (`category_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `size_id` (`size_id`),
  ADD KEY `order_items_ibfk_1` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_categories`
--
ALTER TABLE `product_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `product_sizes`
--
ALTER TABLE `product_sizes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `size_id` (`size_id`);

--
-- Indexes for table `sizes`
--
ALTER TABLE `sizes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `tokens`
--
ALTER TABLE `tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_wishlist` (`user_id`,`product_id`,`size_id`),
  ADD KEY `size_id` (`size_id`),
  ADD KEY `wishlist_ibfk_2` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT for table `product_sizes`
--
ALTER TABLE `product_sizes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=537;

--
-- AUTO_INCREMENT for table `sizes`
--
ALTER TABLE `sizes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `tokens`
--
ALTER TABLE `tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `addresses`
--
ALTER TABLE `addresses`
  ADD CONSTRAINT `addresses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `cart_ibfk_3` FOREIGN KEY (`size_id`) REFERENCES `sizes` (`id`),
  ADD CONSTRAINT `fk_cart_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cart_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cart_size` FOREIGN KEY (`size_id`) REFERENCES `sizes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_3` FOREIGN KEY (`size_id`) REFERENCES `sizes` (`id`);

--
-- Constraints for table `product_categories`
--
ALTER TABLE `product_categories`
  ADD CONSTRAINT `product_categories_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_categories_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_sizes`
--
ALTER TABLE `product_sizes`
  ADD CONSTRAINT `product_sizes_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_sizes_ibfk_2` FOREIGN KEY (`size_id`) REFERENCES `sizes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sizes`
--
ALTER TABLE `sizes`
  ADD CONSTRAINT `sizes_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tokens`
--
ALTER TABLE `tokens`
  ADD CONSTRAINT `tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `wishlist_ibfk_3` FOREIGN KEY (`size_id`) REFERENCES `sizes` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
