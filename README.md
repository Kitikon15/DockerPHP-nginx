# 🌐 Kitikon.dev — Developer Portfolio & Docker LEMP Stack

ยินดีต้อนรับสู่โปรเจกต์ **Portfolio ข้อมูลส่วนตัวของ Kitikon (Kitikon15)** และระบบเว็บแอปพลิเคชันบน **Docker LEMP Stack** (Linux, Nginx, MariaDB, PHP-FPM)

---

## 📑 สารบัญ (Table of Contents)
1. [ภาพรวมของไฟล์ index.html และ portfolio.html](#-ภาพรวมของไฟล์-indexhtml-และ-portfoliohtml)
2. [โครงสร้างไฟล์ในโปรเจกต์](#-โครงสร้างไฟล์ในโปรเจกต์)
3. [รายละเอียดฟังก์ชันการทำงานภายในหน้าเว็บ](#-รายละเอียดฟังก์ชันการทำงานภายในหน้าเว็บ)
4. [คำสั่งและวิธีการรันแต่ละรูปแบบ (How to Run)](#-คำสั่งและวิธีการรันแต่ละรูปแบบ-how-to-run)
   - [วิธีที่ 1: เปิดใช้งานโดยตรงผ่าน Browser (Standalone)](#วิธีที่-1-เปิดใช้งานโดยตรงผ่าน-browser-standalone)
   - [วิธีที่ 2: รันผ่าน Docker LEMP Stack (เซิร์ฟเวอร์จำลอง)](#วิธีที่-2-รันผ่าน-docker-lemp-stack-เซิร์ฟเวอร์จำลอง)
   - [วิธีที่ 3: รันด้วย Local Server (Python / Node.js / VS Code Live Server)](#วิธีที่-3-รันด้วย-local-server-python--nodejs--vs-code-live-server)
5. [ข้อมูลระบบฐานข้อมูลและไฟล์เสริมในโปรเจกต์](#-ข้อมูลระบบฐานข้อมูลและไฟล์เสริมในโปรเจกต์)
6. [แนวทางการปรับแต่งข้อมูลเพิ่มเติม (Customization)](#-แนวทางการปรับแต่งข้อมูลเพิ่มเติม-customization)

---

## 🌟 ภาพรวมของไฟล์ index.html และ portfolio.html

ทั้งไฟล์ **`index.html`** และ **`portfolio.html`** ถูกออกแบบเป็น **Single File HTML** (รวมโค้ด HTML, CSS ใน `<style>` และ JavaScript ใน `<script>` ไว้ภายในไฟล์เดียวกันทั้งหมด) โดยไม่ต้องติดตั้ง Library หรือ Compiler เพิ่มเติม

* **`index.html`**: เหมาะสำหรับเป็นหน้าหลัก (Home Page) สามารถดับเบิลคลิกเปิดบนเบราว์เซอร์ได้ทันที หรือนำไป Deploy บน GitHub Pages, Vercel, Netlify
* **`portfolio.html`**: เป็นไฟล์คู่ขนานสำหรับเรียกใช้งานผ่าน Docker Nginx Web Server (`http://localhost:88/portfolio.html`) เพื่อไม่ให้ชนกับค่าเริ่มต้นของ `index.php`

### 🎨 สไตล์และการออกแบบ
* **ธีม:** Modern Dark Tech Mode (โทนสีดำ Obsidian `#0a0f1d`, การ์ดสีน้ำเงินเข้ม `#11192e`, ตัดด้วยสีไฟนีออนฟ้า Sky Blue `#38bdf8` และม่วง Neon Violet)
* **รูปโปรไฟล์:** ดึงจาก GitHub จริงของเจ้าของผลงาน (`https://github.com/Kitikon15.png`) พร้อมกรอบแสงนีออนเคลื่อนไหว (Floating Glow Effect)
* **ฟอนต์:** Google Fonts ภาษาไทย/อังกฤษ (`Prompt` และ `Kanit`)
* **ไอคอน:** FontAwesome 6 (CDN)
* **Responsive:** รองรับทั้งหน้าจอมือถือ แท็บเล็ต และคอมพิวเตอร์อย่างสมบูรณ์แบบ

---

## 📂 โครงสร้างไฟล์ในโปรเจกต์

```text
D:\67.50\
│
├── docker-compose.yml       # ไฟล์ตั้งค่า Docker Containers (Nginx, PHP, MariaDB)
├── README.md                # เอกสารคู่มืออธิบายการใช้งานโปรเจกต์
│
├── public_html/             # โฟลเดอร์เว็บรูท (Document Root สำหรับ Nginx)
│   ├── index.html           # หน้า Portfolio สำหรับเปิดดูตรงๆ หรือใช้เป็น Entrypoint
│   ├── portfolio.html       # หน้า Portfolio (Single File HTML) สำหรับเรียกดูผ่าน Web Server
│   ├── index.php            # ไฟล์ทดสอบการเชื่อมต่อฐานข้อมูลเบื้องต้น
│   ├── pdo_data.php         # ไฟล์เชื่อมต่อฐานข้อมูล PDO + Dashboard ตรวจสอบสถานะ DB
│   └── show_data.php        # ระบบแสดงและค้นหาข้อมูลผู้โดยสารเรือไททานิค (Titanic Dataset)
│
├── mariadb/                 # ฐานข้อมูล MariaDB
│   ├── initdb/              # ไฟล์ SQL สำหรับ Initialize ข้อมูล (titanic.sql)
│   └── data/                # วอลุ่มจัดเก็บข้อมูลฐานข้อมูลอย่างถาวร
│
├── nginx/                   # คอนฟิกูเรชันเซิร์ฟเวอร์ Nginx
│   ├── conf/nginx.conf      # คอนฟิกหลักของ Nginx
│   └── conf.d/default.conf  # คอนฟิก Virtual Host และ FastCGI สำหรับ PHP
│
└── php/                     # การสร้างอิมเมจ PHP
    └── Dockerfile           # Dockerfile ติดตั้ง Extension ที่จำเป็น (pdo, pdo_mysql, mysqli)
```

---

## 🧩 รายละเอียดฟังก์ชันการทำงานภายในหน้าเว็บ

หน้าเว็บ Portfolio แบ่งออกเป็น 7 ส่วนหลัก:

1. **Navbar (แถบนำทางด้านบน):**
   - โลโก้แบรนด์ `Kitikon.dev`
   - เมนู Smooth Scroll ไปยังส่วนต่างๆ (หน้าแรก, เกี่ยวกับฉัน, ทักษะ, ผลงาน, ติดต่อ)
   - ปุ่มทางลัดไปยัง GitHub Profile (`@Kitikon15`)
   - เมนู Hamburger แบบพับเก็บได้สำหรับหน้าจอมือถือ

2. **Hero Section (ส่วนแนะนำตัวหลัก):**
   - กรอบรูปวงกลม Glow Effect ดึงรูปสดจาก `https://github.com/Kitikon15.png`
   - ป้ายสถานะจำลอง "พร้อมรับงาน & ร่วมงานโปรเจกต์ใหม่ๆ"
   - ชื่อ **Kitikon** และตำแหน่ง **Developer / Software Creator**
   - ปุ่ม Call to Action: *"ดูผลงาน (Projects)"* และ *"GitHub Profile"*
   - ไอคอนลิงก์ Social Media (GitHub, Email, LinkedIn, Facebook)

3. **About Me (เกี่ยวกับฉัน):**
   - **Terminal Code Window (`profile.json`):** หน้าต่างจำลอง Terminal สไตล์ Developer แสดงข้อมูลโปรไฟล์ในรูปแบบ JSON
   - คำบรรยายเป้าหมายและแนวคิดการพัฒนา Clean Code & Scalable Architecture
   - ไฮไลต์การ์ด 4 ทักษะสำคัญ: Full-Stack Ready, Containerized, Clean Architecture, Fast Learner

4. **Skills (ทักษะและความสามารถ):**
   - **Front-End:** HTML5, CSS3, JavaScript (ES6+), Bootstrap 5, React.js, Responsive Web
   - **Back-End & DB:** Node.js/Express, PHP (PDO), MariaDB/MySQL, Java, Python, RESTful APIs
   - **Tools & Web3:** Docker & Compose, Nginx, Git/GitHub, Solidity (Ethereum), Linux/Bash, VS Code

5. **Projects (ผลงานจาก GitHub ของ Kitikon15):**
   - **DockerPHP-nginx:** ระบบ LEMP Stack บน Docker พร้อมระบบสืบค้นข้อมูลผู้โดยสารเรือไททานิค
   - **BackEnd-Product:** RESTful API Service สำหรับการจัดการแคตตาล็อกสินค้า (Node.js/Express)
   - **BlockChain & Smart Contracts:** พัฒนา Smart Contract บน Ethereum ด้วย Solidity
   - **Algorithm & Data Structures:** รวบรวมการวิเคราะห์อัลกอริทึมและโครงสร้างข้อมูลด้วย Java
   - ลิงก์ตรงไปยัง GitHub Repositories แต่ละตัว

6. **Contact (ติดต่อฉัน):**
   - ช่องทางติดต่อตรง: GitHub, อีเมล, สถานที่
   - **Interactive Contact Form:** ฟอร์มส่งข้อความ พร้อมการแจ้งเตือนผ่าน JavaScript Alert เมื่อกรอกข้อมูลครบถ้วน

7. **Footer & Back to Top:**
   - ข้อมูลลิขสิทธิ์ประจำปีอัตโนมัติ
   - ปุ่มลอย **Back to Top** เลื่อนกลับขึ้นด้านบนแบบนุ่มนวล

---

## 🚀 คำสั่งและวิธีการรันแต่ละรูปแบบ (How to Run)

### วิธีที่ 1: เปิดใช้งานโดยตรงผ่าน Browser (Standalone)
วิธีนี้ง่ายและรวดเร็วที่สุด ไม่ต้องติดตั้งหรือเปิดโปรแกรมเซิร์ฟเวอร์ใดๆ

* **ใช้เมาส์:**
  1. เข้าไปที่โฟลเดอร์ `D:\67.50\public_html\`
  2. ดับเบิลคลิกไฟล์ `index.html` หรือ `portfolio.html`
  3. ไฟล์จะเปิดขึ้นบนเว็บเบราว์เซอร์เริ่มต้นของคุณทันที (Google Chrome, Microsoft Edge ฯลฯ)

* **ใช้คำสั่งผ่าน PowerShell:**
  ```powershell
  # เปิดไฟล์ index.html บน Browser
  Start-Process "D:\67.50\public_html\index.html"

  # หรือเปิดไฟล์ portfolio.html
  Start-Process "D:\67.50\public_html\portfolio.html"
  ```

---

### วิธีที่ 2: รันผ่าน Docker LEMP Stack (เซิร์ฟเวอร์จำลอง)
เหมาะสำหรับการทดสอบทั้งหน้า Portfolio และระบบหลังบ้าน PHP/MariaDB ที่ทำงานบน Nginx Web Server

#### 1. คำสั่งเริ่มการทำงานของเซิร์ฟเวอร์ (Start Containers):
เปิด PowerShell ที่โฟลเดอร์ `D:\67.50\` แล้วรันคำสั่ง:
```powershell
docker compose up -d
```
> ระบบจะรัน 3 คอนเทนเนอร์ในพื้นหลัง: `lemp_nginx` (พอร์ต 88), `lemp_php` (พอร์ต 9000), และ `lemp_mariadb` (พอร์ต 3306)

#### 2. ลิงก์สำหรับเข้าชมบนเว็บเบราว์เซอร์:
* **หน้า Portfolio:** [http://localhost:88/portfolio.html](http://localhost:88/portfolio.html) หรือ [http://localhost:88/index.html](http://localhost:88/index.html)
* **หน้าแสดงข้อมูล Titanic (PDO):** [http://localhost:88/show_data.php](http://localhost:88/show_data.php)
* **หน้าตรวจสอบการเชื่อมต่อฐานข้อมูล:** [http://localhost:88/pdo_data.php](http://localhost:88/pdo_data.php)
* **หน้าทดสอบเดิม:** [http://localhost:88/index.php](http://localhost:88/index.php)

#### 3. คำสั่งตรวจสอบสถานะคอนเทนเนอร์ (Check Status):
```powershell
docker compose ps
```

#### 4. คำสั่งดู Log การทำงาน (View Logs):
```powershell
# ดู Log ทั้งหมดแบบ Real-time
docker compose logs -f

# ดู Log เฉพาะของ Nginx Web Server
docker compose logs -f nginx
```

#### 5. คำสั่งหยุดการทำงานของเซิร์ฟเวอร์ (Stop Containers):
```powershell
# หยุดการทำงาน
docker compose stop

# หรือหยุดและลบ Container เครือข่ายออก
docker compose down
```

---

### วิธีที่ 3: รันด้วย Local Server (Python / Node.js / VS Code Live Server)
หากต้องการทดสอบผ่าน HTTP Server ในเครื่องโดยไม่ใช้ Docker:

* **รันด้วย Python:**
  ```powershell
  cd D:\67.50\public_html
  python -m http.server 3000
  ```
  เข้าชมได้ที่: `http://localhost:3000`

* **รันด้วย Node.js (npx serve):**
  ```powershell
  cd D:\67.50\public_html
  npx serve .
  ```

* **รันผ่าน VS Code Live Server:**
  1. เปิดโฟลเดอร์ `D:\67.50` ใน VS Code
  2. คลิกขวาที่ไฟล์ `index.html` หรือ `portfolio.html`
  3. เลือก **"Open with Live Server"**

---

## 🗄️ ข้อมูลระบบฐานข้อมูลและไฟล์เสริมในโปรเจกต์

| ชื่อไฟล์ | คำอธิบายหน้าที่ |
| :--- | :--- |
| **`pdo_data.php`** | ใช้เชื่อมต่อฐานข้อมูล MariaDB ผ่านคลาส `PDO` พร้อม UI Dashboard แสดงสถานะการเชื่อมต่อ และจำนวนแถวข้อมูล |
| **`show_data.php`** | ดึงข้อมูลผู้โดยสารเรือไททานิคจากฐานข้อมูลมาแสดงในตาราง Bootstrap 5 พร้อมระบบค้นหา, ตัวกรองตามชั้นที่นั่ง/การรอดชีวิต และระบบแบ่งหน้า (Pagination) |
| **`index.php`** | ไฟล์ทดสอบการเชื่อมต่อแบบดั้งเดิมผ่านฟังก์ชัน `mysqli` |

### ข้อมูลการเชื่อมต่อฐานข้อมูล MariaDB:
* **Host:** `db` (สำหรับรันภายใน Docker Container) หรือ `localhost` (พอร์ต 3306)
* **Database Name:** `sample_db` หรือ `titanic`
* **Username:** `admin` (หรือ `root`)
* **Password:** `1234`
* **ตารางข้อมูล:** `titanic` (มีข้อมูลผู้โดยสารจำนวน 891 รายการ)

---

## 🛠️ แนวทางการปรับแต่งข้อมูลเพิ่มเติม (Customization)

หากต้องการปรับปรุงเนื้อหาใน `index.html` หรือ `portfolio.html`:

1. **เปลี่ยนข้อมูลส่วนตัว:**
   - ค้นหาคำว่า `Kitikon` หรือ `Kitikon15` ในไฟล์ แล้วแก้ไขเป็นข้อความที่คุณต้องการ
2. **เปลี่ยนรูปโปรไฟล์:**
   - ค้นหาแท็ก `<img src="https://github.com/Kitikon15.png" ...>` หากต้องการใช้รูปภาพในเครื่อง ให้สร้างโฟลเดอร์เช่น `images/profile.jpg` แล้วใส่ path แทนที่
3. **ปรับแก้รายการผลงาน (Projects):**
   - ไปที่ส่วน `<section class="section" id="projects">` แล้วแก้ไขชื่อ รายละเอียด หรือเพิ่มการ์ดโปรเจกต์ใหม่ตามต้องการ
4. **เปลี่ยนอีเมลติดต่อ:**
   - ค้นหา `kitikon15.dev@example.com` แล้วแทนที่ด้วยอีเมลจริงที่ต้องการให้ติดต่อ

---

<p align="center">
  พัฒนาโดย <strong>Kitikon (Kitikon15)</strong> • ขับเคลื่อนด้วยพลังแห่งการเรียนรู้และการเขียนโค้ด 🚀
</p>
