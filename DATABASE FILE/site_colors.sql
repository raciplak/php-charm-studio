-- Site Color Settings Table
-- Run this SQL in your MySQL database (ecommerceweb)

CREATE TABLE `tbl_site_colors` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `color_key` VARCHAR(100) NOT NULL UNIQUE,
  `color_value` VARCHAR(20) NOT NULL DEFAULT '#000000',
  `color_label` VARCHAR(255) NOT NULL,
  `color_group` VARCHAR(100) NOT NULL DEFAULT 'Genel',
  `display_order` INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default color values
INSERT INTO `tbl_site_colors` (`color_key`, `color_value`, `color_label`, `color_group`, `display_order`) VALUES
('top-bar-bg', '#131921', 'Üst Bar Arka Plan', 'Header', 1),
('navbar-bg', '#232f3e', 'Navbar Arka Plan', 'Header', 2),
('search-btn-color', '#ff6b35', 'Arama Butonu', 'Header', 3),
('header-icon-color', '#131921', 'Header İkon Rengi', 'Header', 4),
('primary-color', '#0d1452', 'Ana Renk (Primary)', 'Genel', 10),
('accent-color', '#e4144d', 'Vurgu Rengi (Accent)', 'Genel', 11),
('cart-btn-color', '#e7a340', 'Sepete Ekle Butonu', 'Butonlar', 20),
('btn-hover-color', '#333333', 'Buton Hover Rengi', 'Butonlar', 21),
('discount-color', '#e74c3c', 'İndirim / Fiyat Etiketi', 'Butonlar', 22),
('footer-bg', '#2c3e50', 'Footer Arka Plan', 'Footer', 30),
('footer-bottom-bg', '#1a252f', 'Footer Alt Kısım Arka Plan', 'Footer', 31),
('footer-text-color', '#bdc3c7', 'Footer Yazı Rengi', 'Footer', 32),
('footer-accent-color', '#e4144d', 'Footer Vurgu Rengi', 'Footer', 33),
('page-overlay-bg', '#131921', 'Sayfa Banner Overlay', 'Genel', 12),
('mobile-menu-bg', '#1a252f', 'Mobil Menü Arka Plan', 'Header', 5),
('newsletter-bg', '#232f3e', 'Bülten Bölümü Arka Plan', 'Genel', 13),
('slider-btn-color', '#e4144d', 'Slider Tıkla Butonu', 'Slider', 40);
