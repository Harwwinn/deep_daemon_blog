-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 21-09-2025 a las 15:54:48
-- Versión del servidor: 11.4.8-MariaDB-cll-lve-log
-- Versión de PHP: 8.4.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `deepdae1_bark_blogdb`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `articulos`
--

CREATE TABLE `articulos` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `contenido` longtext NOT NULL,
  `imagen_destacada` varchar(255) DEFAULT NULL,
  `categoria_id` int(10) UNSIGNED DEFAULT NULL,
  `visitas` int(11) DEFAULT 0,
  `fecha_publicacion` datetime DEFAULT current_timestamp(),
  `autor_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `articulos`
--

INSERT INTO `articulos` (`id`, `titulo`, `contenido`, `imagen_destacada`, `categoria_id`, `visitas`, `fecha_publicacion`, `autor_id`) VALUES
(30, 'Triple-buffered bi-directional solution', '<h2>Possimus voluptatem totam atque eaque qui harum.</h2><p>Et earum atque dolorem est voluptatem voluptas ab. Atque reprehenderit aut commodi sed. Porro consequatur magni quo eligendi impedit. Eveniet dolorem debitis ipsa nemo amet aut.\n\nMagni libero assumenda accusantium similique dolorum quis. Commodi saepe at architecto. Earum hic eaque eaque ad quo. Assumenda animi dolor assumenda deserunt.\n\nEt deserunt quia sint expedita tempora dignissimos autem. Ab quas pariatur perspiciatis voluptas. Commodi quae est rem ipsum cumque enim et. Temporibus impedit voluptatibus quos vel et et.\n\nEaque ut et velit nobis eum et rerum quia. Explicabo cum animi dolorem dolorem. Ratione fugiat mollitia veniam est quidem. Voluptatum harum dignissimos ut.</p><h3>Voluptas nihil omnis qui voluptate non quis perferendis.</h3><blockquote><p>Accusantium repudiandae assumenda vel totam voluptatem labore ad provident quo nemo sit ea sed.</p></blockquote><p>Adipisci in mollitia magni rerum. Et corrupti qui aut minus. Maiores id consequatur nulla et ut corporis. Rem repellendus ex asperiores id voluptatem et iure sed.\n\nOptio quam quis provident. Officiis sit qui eius enim ut quasi laborum.\n\nNeque vero eos praesentium veritatis sit in delectus. Enim minima inventore accusamus ut recusandae qui. Ad eveniet quia nobis quia sit hic est.</p><ul><li>ad ea molestiae</li><li>nam delectus non</li><li>et iste esse</li></ul>', 'gertruda-valaseviciute-xMObPS6V_gY-unsplash.jpg', 2, 31, '2025-06-11 04:22:05', 5),
(31, 'Implemented client-server pricingstructure', '<h2>Voluptas aut odit iusto velit placeat.</h2><p>Sed et quis repellendus dolores voluptate quam. Quisquam reiciendis occaecati et. Id maxime tempora magnam non odio aut voluptates ad.\n\nMolestiae quia saepe voluptate eum autem et. Voluptas voluptas voluptatem sequi enim eum aliquid et. Molestiae dolorem optio labore autem sapiente consequatur ipsa. Et quae cum rerum a unde.\n\nEum et error ducimus illum inventore dignissimos cupiditate inventore. Vitae dolorem error ut error qui architecto aspernatur non. Nisi ut dolore suscipit nostrum quibusdam et. Qui quae placeat dolore similique deleniti assumenda odit nostrum.\n\nNobis est in dolor suscipit quod. Aliquid odio laboriosam sunt deleniti repellendus sed. Possimus voluptas libero dolores aperiam dignissimos et voluptatibus.</p><h3>Sint iure vel molestiae sint neque nihil aut sit.</h3><blockquote><p>Esse explicabo suscipit quia sint totam deserunt eos dolores cumque et.</p></blockquote><p>Qui est eligendi natus dolorem laboriosam deleniti harum mollitia. Libero qui sunt et. Voluptas eaque nemo id voluptatem. Quod accusamus repellat ullam nobis.\n\nOfficiis blanditiis reprehenderit cum. Laborum sint est inventore dignissimos in quia. Rerum sed doloremque id veritatis. Voluptas incidunt ratione tempora.\n\nNihil dolorem esse rem tempora qui quia. Provident fuga magnam aliquam quibusdam similique. Accusamus ex qui doloremque impedit odit.</p><ul><li>eos accusantium illo</li><li>corrupti repellendus delectus</li><li>tempore unde sequi</li></ul>', 'luca-bravo-XJXWbfSo2f0-unsplash.jpg', 1, 21, '2025-07-23 22:03:44', 5),
(32, 'Vision-oriented incremental challenge', '<h2>Voluptates autem maxime illo cumque.</h2><p>Sit in aut accusantium. Consequatur nihil vel dolorum sit qui. Aliquid incidunt error autem cum aut. Maxime corporis nulla dolores animi earum. Eum ullam exercitationem ratione facere exercitationem sunt.\n\nAut inventore delectus maxime sit recusandae. Autem ullam perspiciatis amet voluptate corporis voluptatibus accusantium eos. Quasi architecto quia voluptatum in. Autem sit porro dolorum beatae ullam voluptatem.\n\nSoluta repellat occaecati qui quos similique. Iusto rerum qui voluptatibus impedit non.\n\nQuo quae a quibusdam nemo quod vero. Beatae ut suscipit porro corrupti amet cumque et. Aut officiis velit voluptatem quae provident sunt eius. Et neque excepturi rerum nobis. Quia voluptates rerum nam vero natus delectus.</p><h3>Reprehenderit id non vel neque.</h3><blockquote><p>Qui veritatis quia perspiciatis qui minima voluptatem suscipit totam iure dicta ipsum aliquam.</p></blockquote><p>Iusto aperiam et quaerat nulla ea qui. Possimus ducimus quo possimus beatae id voluptas deleniti. Praesentium sit totam dicta ut ut cupiditate similique.\n\nRepellendus atque dolores voluptatem dolorum molestias dicta eveniet facere. Autem quia voluptatem corrupti accusantium eius quae. Sit nihil similique consequatur autem eius enim numquam.\n\nQuaerat tenetur ratione velit quod doloremque sunt quia. Id est porro quia unde molestiae sed qui. Et consequuntur nisi quae. Voluptates ut sed architecto ut aut ut. Dolorem rerum quas non accusamus accusantium ea eum.</p><ul><li>cupiditate illum vitae</li><li>et quia non</li><li>modi in amet</li></ul>', 'alex-knight-2EJCSULRwC8-unsplash.jpg', 4, 1, '2025-03-06 20:17:37', 5),
(34, 'Virtual even-keeled productivity', '<h2>Ab non doloremque sunt cupiditate non quis.</h2><p>Deserunt qui itaque perferendis qui illo sequi error. Eum consequatur in numquam at molestiae aut quam tenetur. Suscipit odio nisi corporis accusantium. In optio pariatur praesentium voluptatem nesciunt est commodi magni.\n\nAtque dolorem dolores impedit id. Quibusdam aut sunt unde earum omnis est. Debitis quae beatae voluptas et eos qui occaecati. Dicta culpa possimus at possimus quia.\n\nNumquam deleniti cum commodi ipsum aliquid veniam blanditiis. Quia et atque illo et iusto. Assumenda voluptatem modi ipsum excepturi.\n\nEos ducimus veritatis enim est praesentium a. Dolores atque repellat iure eveniet veritatis fugit incidunt.</p><h3>Repudiandae cum explicabo maiores.</h3><blockquote><p>Ex vero veniam earum iste sunt ut eum corrupti architecto mollitia at autem eos dolor explicabo.</p></blockquote><p>Distinctio dolorem dignissimos voluptates id. Asperiores consequatur deserunt et deleniti officia. Quod quaerat qui id aperiam nobis maxime. Quisquam voluptatem et perspiciatis voluptate reiciendis in eveniet a.\n\nDeleniti neque iusto et rerum quo voluptates quos. Aut et architecto impedit sint eos aut. Perspiciatis ut enim natus possimus. Delectus dolor ut ex est officiis et sint.\n\nNesciunt sit ut et tempora. Veniam repudiandae architecto quidem aspernatur dolore ad. Quia et enim inventore et natus.</p><ul><li>quo rerum rerum</li><li>quia dolorum dignissimos</li><li>qui voluptatibus cumque</li></ul>', 'luca-bravo-XJXWbfSo2f0-unsplash.jpg', 4, 1, '2025-03-03 18:33:38', 5),
(36, 'Devolved human-resource encoding', '<h2>Voluptatem consequatur harum dignissimos qui nihil tempore.</h2><p>Exercitationem iure possimus ut et sed non quidem. Possimus dolores eaque dolores veniam. Porro delectus dolores odit porro saepe sint autem.\n\nDeleniti qui quasi sint non blanditiis. Doloribus voluptas quisquam qui illum. Veniam facilis fugiat est esse id.\n\nDolor et enim corrupti. Fugit praesentium ut sint temporibus. Non maiores eum consequatur delectus eius mollitia. Porro aliquam eos eum aliquam velit.\n\nQui autem porro ut et a quidem. Consequuntur veniam dolore unde tempora vel. Qui dolores rem excepturi quia sint modi.</p><h3>Tempora quibusdam sunt non dolorem pariatur.</h3><blockquote><p>Et velit ad eveniet est esse eligendi reprehenderit et voluptatem optio qui.</p></blockquote><p>Porro molestias reiciendis odio ullam. Autem at itaque distinctio eum velit quibusdam.\n\nQui provident cum repellendus hic repellendus porro. Ab occaecati temporibus dolorum nisi ipsa delectus et. Distinctio et impedit quidem.\n\nVel vero velit nobis minima nisi. Cum nesciunt illo expedita et cupiditate ut. Enim sit voluptates dolores aspernatur et inventore voluptate. Laudantium sit et eius rerum harum incidunt delectus aliquid.</p><ul><li>porro at blanditiis</li><li>sed modi iusto</li><li>placeat sint nobis</li></ul>', 'google-deepmind-LaKwLAmcnBc-unsplash.jpg', 1, 47, '2025-08-03 16:10:05', 5),
(37, 'Synergistic zeroadministration utilisation', '<h2>Cum quis aut ipsam.</h2>\r\n<p>Deserunt nostrum id veniam consequatur sed odit. Exercitationem voluptas rem vel molestiae placeat. Deleniti dolorem ut aut eaque qui accusamus neque. Repudiandae qui modi molestias eaque consequuntur. Temporibus aut odit exercitationem corrupti. Quaerat eos eum maiores et perferendis quos. Recusandae placeat amet illo ea. Repellendus dolores quis iusto ut. Ipsam soluta ut et velit et eum. Quaerat fugiat doloremque fugiat cupiditate aut animi et. Quod incidunt aperiam nostrum consequatur. Natus corporis voluptatem ut nihil sed aut. Consectetur voluptate voluptate doloremque unde sunt voluptatem id. Adipisci et iusto quaerat est.</p>\r\n<h3>Laudantium vero excepturi blanditiis cum.</h3>\r\n<blockquote>\r\n<p>Minima temporibus ex aut consequatur quae est vitae aliquam incidunt qui alias explicabo sed.</p>\r\n</blockquote>\r\n<p>Voluptas rerum omnis aperiam beatae dolorem sapiente. Quibusdam asperiores quod quis recusandae. Ut nisi sunt harum eos quisquam. Sed sunt dolores voluptatem debitis odit sunt. Eaque qui aut cupiditate quia. Accusantium non neque sit consectetur consectetur accusantium. Illo odio fuga culpa in numquam. Aut id iure aperiam consequatur. Soluta amet et aut minima consectetur iure eos. Nulla rerum ut facilis error.</p>\r\n<ul>\r\n<li>hic possimus quo</li>\r\n<li>ratione necessitatibus quidem</li>\r\n<li>sint optio cumque</li>\r\n</ul>', 'alex-knight-2EJCSULRwC8-unsplash.jpg', 5, 28, '2025-08-07 15:40:40', 5),
(38, 'Organic background throughput', '<h2>Sed est voluptas ut perspiciatis.</h2><p>Qui provident possimus tempora qui dolores earum. Iure culpa iure iste quidem. Nihil explicabo cum culpa est minima quasi. Repudiandae rerum eveniet rerum iste sunt vel.\n\nPlaceat non aliquid ex consequuntur. Veritatis quo aliquid soluta facere perspiciatis praesentium distinctio natus.\n\nSint voluptatibus alias et ab accusantium a eius. Laboriosam consequatur molestiae quia.\n\nQuam illo tempora doloremque inventore aut hic deserunt fuga. Voluptatum debitis et corporis animi repellat ut odio. Aut magni molestiae voluptatem eveniet. Eligendi at pariatur ab veniam consequuntur sed.</p><h3>Voluptatem aspernatur accusamus suscipit et rerum enim et mollitia.</h3><blockquote><p>Quae omnis quisquam ut sit enim voluptatem et.</p></blockquote><p>Pariatur delectus quasi ipsa quis rem voluptas possimus. Id fuga voluptate ea et sapiente officia.\n\nConsequatur cumque odio nemo repellendus distinctio sequi nesciunt omnis. Quia ex aut eligendi eaque fugiat. Sit mollitia consectetur et.\n\nRecusandae nam doloribus quia. Perspiciatis laboriosam sunt quia iure explicabo quae aut. Illum odio quae pariatur eos quibusdam odio non.</p><ul><li>reiciendis sint ea</li><li>incidunt aut ducimus</li><li>voluptates aut recusandae</li></ul>', 'nasa-Q1p7bh3SHj8-unsplash.jpg', 1, 27, '2025-07-04 23:25:12', 5),
(40, 'Configurable intangible time-frame', '<h2>Non enim id omnis.</h2><p>Alias sunt accusantium repellendus eaque similique minus rerum quas. Vitae corrupti aliquam dolorem. Minus est sint ducimus nesciunt.\n\nTempore sequi eos illum quod laborum ad id et. Ullam nostrum voluptatem sit molestiae exercitationem ducimus est. Et ut officia et id. Debitis vel beatae est culpa quo dolor iure.\n\nUt ut alias cupiditate ipsam architecto nemo repudiandae. Id dolor sapiente dicta. Rerum esse sit error soluta laboriosam. Dolores quibusdam sit soluta fuga. Est corrupti eius ab soluta.\n\nUnde est et et consequatur fuga nostrum et. Provident doloribus minus nobis culpa ea quod.</p><h3>Explicabo consectetur autem quisquam eaque fugiat.</h3><blockquote><p>Et rerum accusantium ut ut eveniet et a repudiandae qui id sapiente velit ullam unde qui.</p></blockquote><p>Aut rerum aut repellat deserunt praesentium dolorem laboriosam. Quia qui quia at eum aliquam dolore doloremque ex. Maiores et a delectus quos quasi assumenda.\n\nOptio quia dolore laborum libero. Eius omnis et sed error. Consectetur necessitatibus qui magnam optio eum distinctio blanditiis et.\n\nArchitecto est fugit odio pariatur. Voluptas et blanditiis perspiciatis maiores eos nisi hic aut. Ducimus numquam ut blanditiis delectus non eos rem.</p><ul><li>sint debitis unde</li><li>voluptas molestiae atque</li><li>aut commodi aut</li></ul>', 'possessed-photography-U3sOwViXhkY-unsplash.jpg', 4, 0, '2025-03-16 07:57:43', 5),
(41, 'Front-line homogeneous functionalities', '<h2>Et provident eos quos.</h2><p>Dolorem inventore voluptatem quis error et. Nostrum non quas est ut maiores. Et sequi magni repellat saepe et.\n\nQui aut eum qui debitis. Distinctio ea odit corporis cumque et hic. Temporibus pariatur iure eum exercitationem. Nihil natus non accusantium neque.\n\nEius unde totam eum exercitationem laboriosam unde voluptas. Eos laborum pariatur esse consequatur ut libero. Autem voluptas quaerat amet dolor ab fugit eligendi. Deserunt ut fugiat nesciunt repellendus.\n\nSed ut deserunt nam qui omnis minima. Deleniti nesciunt nostrum sed. Nobis suscipit ut perferendis quia maiores ut quasi. Quos atque quo est voluptatem vero aperiam.</p><h3>Non id tempore earum numquam neque itaque.</h3><blockquote><p>Et excepturi amet ipsum sed excepturi qui saepe in ducimus consectetur beatae id.</p></blockquote><p>Reiciendis hic ad suscipit odit. Quisquam ut aut in itaque ea.\n\nRepudiandae nobis sunt saepe doloremque necessitatibus. Et culpa voluptatem temporibus culpa possimus consequatur in. Error voluptatem praesentium ipsam et eos dolorem corrupti.\n\nOmnis placeat vel libero in consectetur quis nihil. Enim dolorem dolores quia libero modi. Ab impedit quo rem corrupti voluptas dolores est. Voluptas expedita voluptates reprehenderit dolorem eius. Tempore sed dolorem vitae enim.</p><ul><li>officiis illum ducimus</li><li>possimus quibusdam nam</li><li>et et qui</li></ul>', 'possessed-photography-U3sOwViXhkY-unsplash.jpg', 1, 17, '2025-05-26 10:17:21', 5),
(42, 'Synchronised content-based project', '<h2>Voluptas sapiente non corporis et cum aut.</h2><p>Voluptas quod neque expedita ut nobis. Vel autem molestias facere odit saepe voluptatem. Iste occaecati aut rerum eaque. Aut odit accusantium commodi alias blanditiis qui.\n\nVoluptatem amet illo in laboriosam nulla quae qui. Temporibus voluptatibus est error sed vel blanditiis rerum. Neque est deleniti praesentium. Ut iste omnis corrupti fugiat aspernatur vel sit molestiae.\n\nSint ipsam qui porro voluptatem eveniet nemo consequuntur. Architecto optio nulla ea veniam. Mollitia id ullam in aliquid.\n\nDucimus non voluptatem assumenda omnis aut suscipit. Architecto totam voluptatem aspernatur soluta dolorum aperiam. Et non id esse eum sequi illo. Qui harum modi dolorem quia nesciunt optio. Alias perferendis error non.</p><h3>Molestiae qui ullam et neque numquam nam accusamus.</h3><blockquote><p>Aut ex et eveniet ducimus quia et officiis qui vitae.</p></blockquote><p>Ullam inventore officia quis dolores quod. Et eos nihil sed velit sequi ut. Corrupti recusandae ut atque consectetur rem vitae beatae. Quibusdam repellendus quia iste atque. Non nesciunt vel quas nobis voluptas.\n\nNecessitatibus reprehenderit porro maiores aut ut tempore. Accusamus doloribus est quo temporibus quis ullam aut. Dolore eaque autem velit quam officiis voluptate ducimus.\n\nAccusantium esse et omnis placeat praesentium impedit. Quia iste nesciunt iure autem qui. Perspiciatis et deserunt qui temporibus.</p><ul><li>et aut placeat</li><li>exercitationem ut ut</li><li>quas eius nisi</li></ul>', 'alex-knight-2EJCSULRwC8-unsplash.jpg', 6, 20, '2025-07-20 02:41:26', 5),
(43, 'Optional demand-driven knowledgebase', '<h2>Autem odit veritatis est quibusdam velit.</h2><p>Beatae quaerat earum ut. Commodi at blanditiis dolore sit autem culpa et nulla. Dolores amet aut et sit eligendi perspiciatis mollitia dolorem.\n\nQuis voluptas et quam ratione voluptatum natus molestiae quae. Similique quibusdam et non nihil nam a. Velit nobis natus aut sunt nostrum esse autem. Eaque sed quia recusandae quae consequatur quis qui.\n\nOdit accusamus est et eum nihil dolores placeat. Maiores quasi et maxime officia aliquid repellendus. Officiis sapiente ut rem.\n\nEst et esse optio enim. Tempora sit maxime inventore. Sequi eaque commodi necessitatibus voluptatem velit.</p><h3>Non corporis corporis minus culpa voluptas quisquam itaque.</h3><blockquote><p>Eos odit ut delectus dolorum repudiandae enim nisi repellendus iure.</p></blockquote><p>Mollitia laborum maiores ab aliquid libero ut. Vel consequatur suscipit nam optio veritatis et. Beatae provident aut porro laborum. Consectetur ipsa quod voluptatem voluptatum quis et.\n\nQui voluptatem et molestiae perspiciatis. Molestias cupiditate sed nihil id quisquam quis. Quia in quia corrupti nemo distinctio sed. Error perspiciatis et perspiciatis velit est omnis.\n\nBeatae veritatis eius odio aut voluptas. Consequatur qui eos vero. Ipsa est quia possimus temporibus sunt et.</p><ul><li>corporis et praesentium</li><li>suscipit id facere</li><li>voluptate quae et</li></ul>', 'luca-bravo-XJXWbfSo2f0-unsplash.jpg', 6, 1, '2025-05-25 08:02:06', 5),
(44, 'Visionary optimizing groupware', '<h2>Esse quos magni ut.</h2><p>Et ut rerum enim. Saepe totam fuga quia corporis velit libero voluptas. Culpa impedit repudiandae veniam quia ipsam. Voluptas officia autem repellendus occaecati dolores sed ducimus.\n\nVoluptatem et qui optio molestiae. Unde sed facere nulla. Ut cumque asperiores consequatur vel. Nulla illo ex mollitia numquam.\n\nDeserunt et autem quia minus deserunt esse veritatis. Voluptatem minima aut iusto a omnis quas. Adipisci minus in dolore. Eum ducimus aut ullam et quibusdam eum consequatur.\n\nEligendi molestias possimus sint dolorem quidem distinctio sit. Accusantium commodi illo quaerat itaque. Ipsam voluptates possimus qui commodi sit quos. Occaecati dolor expedita quidem est.</p><h3>Est quisquam dolorum in quo.</h3><blockquote><p>Rerum labore amet omnis perferendis qui quae aliquid.</p></blockquote><p>Iusto dolores minima distinctio repellat. Omnis rerum numquam qui. Nihil provident enim qui itaque repellat qui. Ut id sed quia ut.\n\nConsequatur quas et reiciendis. Similique quasi eligendi ad fuga at. Consequatur distinctio accusamus rerum nesciunt distinctio.\n\nAccusamus omnis impedit amet vitae quia enim voluptatem dolorum. Officiis ad harum eum consequuntur aut aut voluptas. Expedita voluptas quo expedita assumenda assumenda vitae voluptas.</p><ul><li>necessitatibus dolorum quo</li><li>magnam ut unde</li><li>exercitationem qui doloribus</li></ul>', 'nasa-Q1p7bh3SHj8-unsplash.jpg', 1, 43, '2025-07-30 03:57:43', 5),
(45, 'Total asymmetric hub', '<h2>Voluptas quibusdam nihil dolores.</h2><p>Et molestias ipsa autem expedita corrupti. Sed delectus nisi facilis earum. Aspernatur expedita veritatis et ab ad. Sit dolorem exercitationem incidunt eos.\n\nHic aliquam aut officia tempora tempora amet. Sint vel facere est id.\n\nVel provident odit pariatur aut eaque molestiae atque quaerat. Et sint officia incidunt excepturi voluptatem. Architecto omnis quos rerum exercitationem consequuntur.\n\nSequi consequatur nemo ut sunt odio deserunt. Et eos enim voluptatibus dolore dolorem dolorum sapiente. Sunt dolorem sunt porro omnis.</p><h3>Et adipisci consequuntur natus ipsum ut tempora in sed.</h3><blockquote><p>Ipsam sunt est quos dolore iusto dolor deserunt et possimus.</p></blockquote><p>Dignissimos vero qui rerum ut quos. Fuga sint omnis minima perspiciatis debitis vero. Rerum at labore hic nihil. Et ipsam facilis quia inventore nemo praesentium.\n\nIpsum aperiam eius dolores. Aut impedit quis in sunt libero. Non ut in veritatis praesentium temporibus qui. Facilis quae atque et amet maiores voluptatibus aut magnam.\n\nDolorum reprehenderit officiis similique dolores ut. Corrupti sed magni reprehenderit dolorem commodi. Quia doloribus ut mollitia reiciendis molestiae corrupti et.</p><ul><li>harum aut recusandae</li><li>fuga qui consequatur</li><li>alias autem et</li></ul>', 'luca-bravo-XJXWbfSo2f0-unsplash.jpg', 1, 0, '2025-03-07 19:09:52', 5),
(46, 'Seamless 4thgeneration architecture', '<h2>Laborum qui quae sint omnis qui.</h2><p>Dolor adipisci sit dolorum voluptatem recusandae perspiciatis. Tempore omnis eius eveniet rerum. Quo quia non eaque distinctio veniam culpa perferendis.\n\nQui quo a dignissimos adipisci at aut. Debitis et rem blanditiis sint dolorem. Et eveniet occaecati sit deserunt aut et voluptatibus ut. Ut commodi minima placeat delectus assumenda modi iure.\n\nHarum molestias et error dignissimos eos. Sed necessitatibus debitis sed deserunt ut praesentium. Quia autem sed esse maxime eum consectetur maxime. Earum at reiciendis quas.\n\nSapiente laborum et autem et sit. Quisquam magnam adipisci incidunt. Et minima adipisci qui sint est assumenda. Iusto similique quis et totam.</p><h3>Et et rerum quo dolorem libero.</h3><blockquote><p>Itaque facere ea est quidem similique hic quia.</p></blockquote><p>Et occaecati porro consequatur et. Sunt et architecto eveniet necessitatibus doloremque possimus et quidem. Enim occaecati assumenda eius neque est eveniet ducimus. Qui eum id nulla aliquid consequuntur facilis sit.\n\nItaque vel id ut. Possimus totam dolor adipisci eum.\n\nEarum illo consequuntur beatae consectetur aut. Velit qui debitis voluptatem illo id blanditiis repellendus. Cupiditate sed asperiores asperiores harum sapiente dolorum. Excepturi illo suscipit eius quam eos.</p><ul><li>neque quo molestiae</li><li>qui et repellat</li><li>pariatur ipsam omnis</li></ul>', 'gertruda-valaseviciute-xMObPS6V_gY-unsplash.jpg', 4, 0, '2025-03-16 01:52:34', 5),
(48, 'Quality-focused full-range systemengine', '<h2>Ipsa laboriosam a velit dolores perferendis.</h2><p>Fugit omnis minus reiciendis distinctio inventore. Modi et consequuntur qui nihil architecto quia cumque.\n\nSint quia maiores dignissimos consequatur quidem quam aliquid. Minus illo ea quia sunt. Eos commodi autem animi et sint praesentium voluptatem. Ab voluptate quia officiis qui.\n\nNecessitatibus dolore impedit et sint illo cum. Voluptas et minus consectetur ea. Dolorem voluptates optio illum quasi. Et cumque incidunt explicabo. Numquam odio accusantium inventore quisquam necessitatibus.\n\nDeserunt consequatur incidunt saepe veniam ut. Velit et occaecati qui nulla provident tenetur. Dicta aspernatur et molestias molestiae saepe.</p><h3>Unde cumque numquam suscipit accusantium id.</h3><blockquote><p>Quaerat cupiditate ab quidem incidunt deserunt aut corrupti et vel exercitationem ut et quidem.</p></blockquote><p>Enim aut a deserunt cupiditate magni exercitationem. Tempore distinctio similique qui non ipsum. Ipsum beatae qui quas enim nesciunt ipsum quisquam ipsam. Quos nulla molestias rerum quae eligendi cum ex.\n\nEt dicta adipisci corrupti nostrum quibusdam omnis. Voluptatem fuga autem facere odio. Beatae earum soluta rerum accusantium incidunt.\n\nVoluptas laudantium illum inventore reiciendis. Nisi ipsa laboriosam harum tenetur laboriosam. Qui architecto at qui a.</p><ul><li>nam harum est</li><li>at sed et</li><li>facere cum eos</li></ul>', 'google-deepmind-LaKwLAmcnBc-unsplash.jpg', 6, 0, '2025-03-03 06:57:22', 5),
(49, 'Managed directional monitoring', '<h2>Magnam error earum adipisci expedita nulla.</h2><p>Aut tempora ut fugit aspernatur. Excepturi consequuntur totam culpa enim. Voluptas exercitationem quia quis in.\n\nVelit in et et modi possimus qui enim soluta. Odio ut cumque et optio eius. Facere ut sint qui ut veniam vero.\n\nItaque exercitationem accusamus at ratione. Consequatur harum et excepturi nisi. Dignissimos animi voluptate pariatur tenetur sit.\n\nVelit voluptas possimus voluptatum natus consequuntur. Quisquam vero unde eum reprehenderit. Doloribus quia eius totam eum. Ea voluptas ut aut et provident.</p><h3>Asperiores non voluptas sunt recusandae accusamus.</h3><blockquote><p>Et vitae sunt omnis rerum soluta modi dolores minima et maiores.</p></blockquote><p>Quas modi eos culpa quas inventore est consequuntur facilis. Praesentium perspiciatis animi distinctio alias perferendis consequatur. Quisquam voluptas eum voluptatem. Non earum ipsum enim qui facilis provident.\n\nFuga placeat voluptas in at doloremque laboriosam veritatis laudantium. Repudiandae dignissimos temporibus sit vitae excepturi nesciunt iusto. Ad pariatur voluptatem ea voluptatum molestiae. Totam repellendus quos aliquam dolor.\n\nQuae aspernatur error et aperiam consequuntur consequatur. Cupiditate et soluta accusantium aliquid doloribus ut minima. Similique culpa omnis molestias.</p><ul><li>corporis voluptas et</li><li>assumenda a quis</li><li>quia nisi rerum</li></ul>', 'nasa-Q1p7bh3SHj8-unsplash.jpg', 2, 0, '2025-03-12 04:14:14', 5),
(50, 'New post written in deploy edited', '<h2>Sed est voluptas ut perspiciatis.</h2>\r\n<p>Qui provident possimus tempora qui dolores earum. Iure culpa iure iste quidem. Nihil explicabo cum culpa est minima quasi. Repudiandae rerum eveniet rerum iste sunt vel. Placeat non aliquid ex consequuntur. Veritatis quo aliquid soluta facere perspiciatis praesentium distinctio natus. Sint voluptatibus alias et ab accusantium a eius. Laboriosam consequatur molestiae quia. Quam illo tempora doloremque inventore aut hic deserunt fuga. Voluptatum debitis et corporis animi repellat ut odio. Aut magni molestiae voluptatem eveniet. Eligendi at pariatur ab veniam consequuntur sed.</p>\r\n<h3>Voluptatem aspernatur accusamus suscipit et rerum enim et mollitia.</h3>\r\n<blockquote>\r\n<p>Quae omnis quisquam ut sit enim voluptatem et.</p>\r\n</blockquote>\r\n<p>Pariatur delectus quasi ipsa quis rem voluptas possimus. Id fuga voluptate ea et sapiente officia. Consequatur cumque odio nemo repellendus distinctio sequi nesciunt omnis. Quia ex aut eligendi eaque fugiat. Sit mollitia consectetur et. Recusandae nam doloribus quia. Perspiciatis laboriosam sunt quia iure explicabo quae aut. Illum odio quae pariatur eos quibusdam odio non.</p>\r\n<ul>\r\n<li>reiciendis sint ea</li>\r\n<li>incidunt aut ducimus</li>\r\n<li>voluptates aut recusandae</li>\r\n</ul>', 'possessed-photography-U3sOwViXhkY-unsplash.jpg', 5, 28, '2025-08-17 20:21:41', 5),
(52, 'Nuevo articulo', '<p><img src=\"../uploads/content_images/img_68a89e3292045.png\" width=\"298\" height=\"298\"></p>', 'steve-johnson-_0iV9LmPDn0-unsplash.jpg', 4, 54, '2025-08-22 10:44:35', 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`, `slug`) VALUES
(1, 'Tecnologia', 'tecnologia'),
(2, 'Web', 'web'),
(4, 'Inteligencia artificial', 'inteligencia-artificial'),
(5, 'Software', 'software'),
(6, 'HardwareEditada', 'hardwareeditada');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `colaboradores`
--

CREATE TABLE `colaboradores` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `rol` varchar(255) NOT NULL,
  `bio` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `enlace_scholar` varchar(255) DEFAULT NULL,
  `enlace_twitter` varchar(255) DEFAULT NULL,
  `enlace_linkedin` varchar(255) DEFAULT NULL,
  `es_fundador` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `colaboradores`
--

INSERT INTO `colaboradores` (`id`, `nombre`, `rol`, `bio`, `foto`, `enlace_scholar`, `enlace_twitter`, `enlace_linkedin`, `es_fundador`) VALUES
(4, 'Marco A. Moreno Armendáriz', 'Colaborador', 'Obtuvo el grado de Licenciatura en Ingeniería Cibernética en la Universidad La Salle, México en 1998 y los grados en Maestro y Doctor en Ciencias en la especialidad de Control Automático en CINVESTAV-IPN en 1999 y 2003, respectivamente. Sus áreas de investigación incluyen las Redes Neuronales Artificiales aplicadas a la identificación y control de sistemas, Visión por Computadora, Mecatrónica y la implementación sobre FPGAs de este tipo de algoritmos.', 'collab_marco-a-moreno-armend-riz_6890693c4a0c0.jpeg', 'https://scholar.google.com.mx/citations?user=zkApqcAAAAAJ&hl=en', 'https://scholar.google.com.mx/citations?user=zkApqcAAAAAJ&hl=en', 'https://scholar.google.com.mx/citations?user=zkApqcAAAAAJ&hl=en', 1),
(5, 'Colaborador 1', 'Colaborador', 'biografía', 'collab_colaborador-1_68a3578b72b56.jpeg', 'https://www.xbox.com/es-MX', 'https://www.xbox.com/es-MX', 'https://www.xbox.com/es-MX', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(150) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `rol` enum('admin','usuario') DEFAULT 'usuario',
  `avatar` varchar(255) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `correo`, `contrasena`, `rol`, `avatar`, `reset_token`, `reset_token_expires_at`) VALUES
(1, 'Diego Rosas Cruz', 'diego@gmail.com', '$2y$10$o5zBjWPFDap5chn0z3vw8O77wy36nBKfFo.Y/AmK8QKAAglKAtM7C', 'usuario', NULL, NULL, NULL),
(5, 'Harwwin', 'rosas.cruz.diego.1@gmail.com', '$2y$10$utd31hyH67QHOWpi.GuxIugBjCuuGzTdOblMyNbAPvIpkA90ogWcq', 'admin', 'avatar9.svg', NULL, NULL),
(6, 'Marco Antonio', 'marco@gmail.com', '$2y$10$mhvIoMvFFhDjuQ4uZNCFKekgGjOuVTNvq3dAbNGiip0f4ItaBKSw2', 'usuario', NULL, NULL, NULL),
(7, 'Diego Rosas 2', 'diego2@gmail.com', '$2y$10$lQasBfU/XYE340fbPRtQNeABZzAOtIcaQwogpSrWI/2QTmOAwWljm', 'usuario', NULL, NULL, NULL),
(8, 'Adolfo Cerqueda', 'adolfo1@gmail.com', '$2y$10$OmnY3tE2WXIFBoyOEeftA.M1FtXjWxZcRu32.2bFbiQuFgIDGjOqa', 'usuario', 'avatar9.svg', NULL, NULL),
(9, 'Jorge Almadaa', 'jorge@gmail.com', '$2y$10$xtdeae0uwTeVsav/7oyUsOAlgPjWPri6l697Wds/QSzmkFaIGVe.y', 'usuario', NULL, NULL, NULL),
(10, 'Albert Camu', 'albert@gmail.com', '$2y$10$YwI1VSM6MsU/eKCCKj.DaegD1x5DERX4QiMgQz0wKPskCxmUd3pl.', 'usuario', NULL, NULL, NULL),
(12, 'Marco Antonio Moreno Armendáriz', 'mam.armendariz@gmail.com', '$2y$10$hCdpKvJuzd4o.qoYd1RUaOcT8AgBIKAdurYw8YDZAOjx23F5h8OEW', 'admin', 'avatar9.svg', NULL, NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `articulos`
--
ALTER TABLE `articulos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `autor_id` (`autor_id`),
  ADD KEY `idx_categoria_id` (`categoria_id`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_nombre` (`nombre`),
  ADD UNIQUE KEY `unique_slug` (`slug`);

--
-- Indices de la tabla `colaboradores`
--
ALTER TABLE `colaboradores`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD KEY `reset_token` (`reset_token`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `articulos`
--
ALTER TABLE `articulos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `colaboradores`
--
ALTER TABLE `colaboradores`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `articulos`
--
ALTER TABLE `articulos`
  ADD CONSTRAINT `articulos_ibfk_1` FOREIGN KEY (`autor_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_articulos_categorias` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
