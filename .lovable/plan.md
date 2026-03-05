

# Site Renk Ayarları - Implementation Plan

## Yapılacaklar

### 1. SQL Migration Dosyası Oluştur
`DATABASE FILE/site_colors.sql` — yeni `tbl_site_colors` tablosu + varsayılan renk kayıtları.

```sql
CREATE TABLE tbl_site_colors (
  id INT AUTO_INCREMENT PRIMARY KEY,
  color_key VARCHAR(100) NOT NULL UNIQUE,
  color_value VARCHAR(20) NOT NULL DEFAULT '#000000',
  color_label VARCHAR(255) NOT NULL,
  color_group VARCHAR(100) NOT NULL DEFAULT 'Genel',
  display_order INT NOT NULL DEFAULT 0
);
```

Varsayılan ~15 renk kaydı INSERT edilecek (üst bar, navbar, primary, accent, butonlar, footer, vb.).

### 2. Admin Sayfası: `admin/site-colors.php`
- `tbl_site_colors` tablosundan tüm renkleri gruplara göre çeker
- Her grup bir `box box-info` içinde gösterilir
- Her renk satırında: Türkçe etiket + `jscolor` renk seçici + hex input
- Tek "Kaydet" butonu tüm renkleri günceller
- Mevcut admin tasarım yapısına uygun

### 3. Admin Menüye Ekleme: `admin/header.php`
"Site Ayarları" satırının altına "Site Renk Ayarları" menü öğesi eklenecek (`fa-paint-brush` ikonu).

### 4. Frontend CSS Değişkenleri Enjekte: `header.php`
Ana sitenin `header.php` dosyasının `<head>` bölümünde `tbl_site_colors` tablosundan renkler okunup `<style>:root { --renk-key: değer; }</style>` olarak yazılacak. Try-catch ile tablo yoksa varsayılan değerler kullanılacak.

### 5. CSS Dosyalarında Hardcoded Renkleri Değiştir
`assets/css/main.css`, `assets/css/side-cart.css`, `assets/css/tree-menu.css` dosyalarındaki hardcoded HEX kodları CSS değişkenlerine dönüştürülecek:

| CSS Değişkeni | Mevcut Değer | Kullanım |
|---|---|---|
| `--top-bar-bg` | `#131921` | Üst bar arka planı |
| `--navbar-bg` | `#232f3e` | Navbar arka planı |
| `--primary-color` | `#0d1452` | Genel butonlar, başlıklar, alt çizgiler |
| `--accent-color` | `#e4144d` | Kategori menü vurgu rengi |
| `--cart-btn-color` | `#e7a340` | Sepete ekle butonu |
| `--search-btn-color` | `#ff6b35` | Arama butonu |
| `--discount-color` | `#e74c3c` | İndirim etiketi |
| `--footer-bg` | `#2a2a2a` | Footer arka planı |
| `--footer-bottom-bg` | `#141314` | Footer alt kısım |
| `--page-overlay-bg` | `#131921` | Sayfa banner overlay |
| `--header-icon-color` | `#131921` | Header ikon rengi |
| `--mobile-menu-bg` | `#1a252f` | Mobil menü arka planı |

### 6. Dosya Listesi

| Dosya | İşlem |
|---|---|
| `DATABASE FILE/site_colors.sql` | Yeni oluştur |
| `admin/site-colors.php` | Yeni oluştur |
| `admin/header.php` | Menü öğesi ekle |
| `header.php` | CSS değişkenleri enjekte et |
| `assets/css/main.css` | ~40+ hardcoded renk → `var()` |
| `assets/css/tree-menu.css` | ~5 hardcoded renk → `var()` |
| `assets/css/side-cart.css` | Varsa hardcoded renk → `var()` |

### Teknik Notlar
- `jscolor.js` zaten `admin/js/` klasöründe mevcut
- Gelecekte yeni renkli alan eklemek: tabloya INSERT + CSS'de `var(--yeni-key)` kullanmak yeterli
- Tablo yoksa try-catch ile varsayılan değerler kullanılacak, site kırılmayacak

