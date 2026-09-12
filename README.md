# 🎓 Lattice IELTS — Nền Tảng Học Thuật & Chấm Điểm IELTS Writing Chuẩn Hóa (Band 4.5 – 5.0)

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-13.x-FF2D20?style=for-the-badge&logo=laravel" alt="Laravel 13" />
  <img src="https://img.shields.io/badge/PHP-8.3%2B-777BB4?style=for-the-badge&logo=php" alt="PHP 8.3" />
  <img src="https://img.shields.io/badge/Tailwind_CSS-v4.0-06B6D4?style=for-the-badge&logo=tailwindcss" alt="Tailwind CSS v4" />
  <img src="https://img.shields.io/badge/Tests-127%20Passed-10B981?style=for-the-badge" alt="Tests" />
  <img src="https://img.shields.io/badge/Rule--Based_Engine-ZERO%20AI-6366F1?style=for-the-badge" alt="Rule-based Zero AI" />
</p>

---

## 🌟 Tổng Quan Dự Án

**Lattice IELTS** là hệ thống web học tập chuyên sâu được thiết kế riêng cho người học ở trình độ **Band 4.5 – 5.0** bứt phá lên **Band 6.0 – 6.5+**. Nền tảng kết hợp hệ thống học từ vựng theo phương pháp Lặp lại ngắt quãng (**Spaced Repetition**), phòng thi mô phỏng **Writing Lab** chia đôi màn hình, cùng **Bộ máy chấm điểm định lượng quy tắc 100% PHP thuần (ZERO AI)** minh bạch, nhất quán và bám sát tiêu chí chấm thi chính thức của IELTS.

---

## 🚀 Các Tính Năng Nổi Bật

### 1. 📝 Rule-Based Scoring Engine (ZERO AI — 100% Thuật toán PHP)
- **Đánh giá đầy đủ 4 tiêu chí IELTS**:
  - **Task Achievement / Task Response (TA/TR)**: Kiểm tra độ dài từ (phạt thiếu từ dưới 150/250 từ), bắt buộc có câu/đoạn Overview trong Task 1 (tối đa Band 5.0 nếu thiếu).
  - **Coherence & Cohesion (CC)**: Đếm số đoạn văn (phạt bài viết 1 đoạn), phát hiện lặp từ nối quá 3 lần, phân tích tính đa dạng của 4 nhóm từ nối học thuật.
  - **Lexical Resource (LR)**: Đo Type-Token Ratio (TTR), đối chiếu kho từ vựng học thuật Academic Word List (AWL), cảnh báo lỗi viết tắt không trang trọng (contractions).
  - **Grammatical Range & Accuracy (GRA)**: Phát hiện câu phức, mệnh đề quan hệ, liên từ phụ thuộc, tính toán tỷ lệ đa dạng cấu trúc.
- **Quy tắc làm tròn Band điểm chính thức**: Tính trung bình cộng 4 tiêu chí và làm tròn về mức $0.0, 0.5, 1.0$ theo thuật toán chuẩn của Cambridge/IDP.

### 2. 📚 Thư Viện Từ Vựng & Lặp Lại Ngắt Quãng (Spaced Repetition)
- Kho từ vựng 4 chủ đề lớn: *Education, Environment, Technology, Health*.
- Đầy đủ phiên âm IPA, phát âm audio, nghĩa tiếng Việt, câu ví dụ song ngữ, collocations, từ đồng nghĩa/trái nghĩa và mẹo viết Task 2.
- Thuật toán ôn tập Spaced Repetition (Leitner 5 cấp độ), hàng đợi ôn từ cần nhớ trong ngày (`Due for review`).
- Flashcard Quiz tương tác cao, thống kê độ chính xác và lịch sử luyện tập.

### 3. ✍️ Phòng Luyện Viết Split-Screen (Writing Lab)
- Giao diện chia đôi màn hình chuẩn phòng thi: Đề bài, gợi ý dàn bài & bài mẫu ở bên trái; Khung soạn thảo, đồng hồ đếm giờ và bộ đếm từ trực tiếp ở bên phải.
- Tự động lưu bản nháp (`Auto-save Drafts`) ngầm mỗi 30 giây.
- Thanh đo số từ theo thời gian thực ($\ge 150$ từ cho Task 1, $\ge 250$ từ cho Task 2).
- Báo cáo kết quả chấm điểm chi tiết kèm biểu đồ trực quan và lời khuyên khắc phục lỗi.

### 4. 📊 Dashboard Học Viên & Quản Trị Viên Toàn Diện
- **Học viên**: Theo dõi chuỗi ngày học liên tục (Study Streak), mục tiêu Target Band, tiến độ bài học, từ vựng cần ôn tập hôm nay.
- **Admin**: Quản lý chủ đề/bài học/từ vựng, ngân hàng đề bài Writing, cấu hình Rubrics & Scoring Rules, danh sách bài nộp của học viên, chấm lại bài viết và nhập nhận xét của giám khảo.

---

## 🔑 Tài Khoản Mẫu (Demo Credentials)

Sau khi chạy Seeder, hệ thống tự động khởi tạo sẵn 2 tài khoản:

| Vai trò | Email | Mật khẩu | Mục đích |
| :--- | :--- | :--- | :--- |
| **Quản trị viên (Admin)** | `admin@ielts.vn` | `password` | Quản lý nội dung, đề bài, rubrics, chấm lại bài nộp |
| **Học viên (Student)** | `student@ielts.vn` | `password` | Học từ vựng, luyện Flashcard, làm bài thi Writing |

---

## 💻 Cài Đặt & Chạy Môi Trường Cục Bộ

### 1. Yêu cầu môi trường
- **PHP** $\ge 8.2$ (khuyến nghị PHP 8.3+)
- **Composer** $\ge 2.5$
- **Node.js** $\ge 18.0$ & **NPM**
- **MySQL** $\ge 8.0$ hoặc **MariaDB**

### 2. Cài đặt các gói phụ thuộc
```bash
# Cài đặt PHP dependencies
composer install

# Cài đặt Frontend dependencies
npm install
```

### 3. Cấu hình môi trường (.env)
```env
APP_NAME="Lattice IELTS"
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lattice_english
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Chạy Migration & Seed Dữ liệu
```bash
php artisan migrate:fresh --seed
```

### 5. Build Assets & Khởi động Server
```bash
# Build frontend assets
npm run build

# Khởi chạy Laravel Development Server
php artisan serve
```

---

## 🧪 Kiểm Thử Tự Động (Automated Testing)

Dự án sở hữu bộ kiểm thử tự động toàn diện từ Unit Test đến Feature Test và End-to-End:

```bash
php artisan test
```

**Kết quả kiểm thử:**
- **127 bài tests** (Unit, Feature, Authorization, Scoring, Seeders, End-to-End).
- **524 assertions** vượt qua 100%.

---

## 🛡️ Bảo Mật & Hiệu Năng
- **Phân quyền chặt chẽ**: Middleware `role:admin`, `role:student`, `onboarded`.
- **Cô lập dữ liệu**: Học viên chỉ có quyền truy cập bài làm và tiến độ cá nhân của chính mình.
- **Chống spam (Rate Limiting)**:
  - Auth attempts: 10 req/min
  - Writing submit: 10 req/min
  - Writing draft: 60 req/min
  - Vocabulary practice: 30 req/min
- **Custom Error Pages**: Giao diện lỗi 403, 404, 419, 500 thân thiện.

---

## 📄 Bản Quyền

Dự án được xây dựng và phát triển trên nền tảng mã nguồn mở Laravel theo chuẩn giấy phép [MIT](LICENSE).
