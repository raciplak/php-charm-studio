-- ============================================
-- TÜM VERİLERİ SİLME SCRİPTİ
-- Veritabanı: ecommerceweb
-- DİKKAT: Bu script tüm verileri kalıcı olarak siler!
-- Tablo yapıları korunur, sadece veriler silinir.
-- ============================================

SET FOREIGN_KEY_CHECKS = 0;

-- Sipariş & Ödeme
TRUNCATE TABLE tbl_order;
TRUNCATE TABLE tbl_payment;

-- Ürün ilişkili tablolar
TRUNCATE TABLE tbl_product_photo;
TRUNCATE TABLE tbl_product_size;
TRUNCATE TABLE tbl_product_color;
TRUNCATE TABLE tbl_rating;
TRUNCATE TABLE tbl_product;

-- Kategori tabloları
TRUNCATE TABLE tbl_end_category;
TRUNCATE TABLE tbl_mid_category;
TRUNCATE TABLE tbl_top_category;

-- Marka & Model
TRUNCATE TABLE tbl_models;
TRUNCATE TABLE tbl_brands;

-- Slider & Fotoğraflar
TRUNCATE TABLE tbl_slider;
TRUNCATE TABLE tbl_photo;

-- Kategori Bannerları
TRUNCATE TABLE tbl_category_banner;

-- FAQ
TRUNCATE TABLE tbl_faq;

-- Müşteriler
TRUNCATE TABLE tbl_customer;

-- Aboneler
TRUNCATE TABLE tbl_subscriber;

-- Renk & Beden & Ülke
TRUNCATE TABLE tbl_color;
TRUNCATE TABLE tbl_size;
TRUNCATE TABLE tbl_country;

-- Paratika ödeme sonuçları (varsa)
-- TRUNCATE TABLE tbl_paratika_payment_results;

-- Servisler
TRUNCATE TABLE tbl_service;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================
-- NOT: tbl_settings ve tbl_admin tabloları 
-- SİLİNMEZ (ayarlar ve admin girişi korunur)
-- ============================================
