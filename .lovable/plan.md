

## Plan: Renk Tablosuna HEX Kod Desteği Ekleme

### Sorun
- `tbl_color` tablosunda sadece `color_id` ve `color_name` var, `color_code` (hex) yok
- Ürün detay sayfası zaten `color_code` sütununu okuyor (line 1188) ama DB'de olmadığı için hep `#cccccc` (gri) gösteriyor — bu yüzden siyah renk gri görünüyor
- Build hatası: `lovable.toml` dosyası eksik

### Yapılacaklar

**1. Veritabanı: `color_code` sütunu ekleme**
- `DATABASE FILE/color_hex_field.sql` dosyası oluşturulacak:
  ```sql
  ALTER TABLE `tbl_color` ADD COLUMN `color_code` VARCHAR(10) DEFAULT '#000000';
  ```

**2. Admin: Renk ekleme formuna hex seçici ekleme (`admin/color-add.php`)**
- Forma `<input type="color">` alanı eklenecek (label: "Renk Kodu")
- INSERT sorgusuna `color_code` dahil edilecek

**3. Admin: Renk düzenleme formuna hex seçici ekleme (`admin/color-edit.php`)**
- Forma `<input type="color">` alanı eklenecek, mevcut değeri gösterecek
- UPDATE sorgusuna `color_code` dahil edilecek

**4. Admin: Renk listesinde hex kodu gösterme (`admin/color.php`)**
- Tabloya renk önizleme sütunu eklenecek (küçük renkli kare + hex kodu)

**5. `lovable.toml` oluşturma**
- Build hatasını düzeltmek için PHP dev server komutu tanımlanacak

### Ürün detay sayfası
- `product.php` zaten `color_code` alanını okuyor ve `background-color` olarak kullanıyor (satır 1188). DB'ye sütun eklenince otomatik çalışacak, değişiklik gerekmez.

