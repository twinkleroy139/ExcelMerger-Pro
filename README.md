# ⚡ ExcelMerger Pro

<div align="center">

**🚀 ExcelMerger Pro — Enterprise Spreadsheet Aggregation & Normalization Platform**

[![Live Demo](https://img.shields.io/badge/Live-Demo-brightgreen?style=for-the-badge&logo=render)](https://excelmerger-pro.onrender.com)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-blue?style=for-the-badge&logo=php)](https://www.php.net/)
[![Python](https://img.shields.io/badge/Python-3.10%2B-yellow?style=for-the-badge&logo=python)](https://www.python.org/)
[![Pandas](https://img.shields.io/badge/Pandas-Data%20Processing-150458?style=for-the-badge&logo=pandas)](https://pandas.pydata.org/)
[![Docker](https://img.shields.io/badge/Docker-Containerized-2496ED?style=for-the-badge&logo=docker)](https://www.docker.com/)
[![License](https://img.shields.io/badge/License-MIT-yellow.svg?style=for-the-badge)](LICENSE)

**An enterprise-grade spreadsheet aggregation and normalization platform for automated batch processing, intelligent header mapping, schema-agnostic deduplication, multi-format exporting, and secure audit tracking.**

[**🌐 Explore Live Application**](https://excelmerger-pro.onrender.com)

</div>

---

## 💡 About The Project

**ExcelMerger Pro** is a high-performance spreadsheet processing platform designed to simplify the process of combining, normalizing, deduplicating, and exporting large batches of spreadsheet files.

The application uses a **hybrid PHP + Python architecture** where PHP handles the presentation, authentication, routing, and web interface while Python provides the high-performance data-processing engine.

Users can upload a `.zip` archive containing multiple spreadsheet files, inspect their structures before processing, configure merge and deduplication rules, and generate a unified master dataset in multiple formats.

The platform also provides **secure tokenized downloads** and **audit telemetry** to track processing results and skipped files.

---

## 🚀 Key Features & Capabilities

- **📦 Batch Spreadsheet Processing**  
  Upload `.zip` archives containing multiple `.xlsx`, `.xls`, or `.csv` files for automated processing.

- **🔍 Async Pre-Merge Structural Inspector**  
  Inspects uploaded spreadsheet structures through AJAX before starting the main merge operation.

- **🧩 Dynamic Header Normalization**  
  Standardizes column headers by trimming whitespace, normalizing case, and applying configurable aliases.

- **📖 Intelligent Header Mapping**  
  Uses `aliases.json` to map different column naming conventions into a unified schema.

- **🧹 Configurable Deduplication Engine**  
  Supports both full-row duplicate detection and custom key-based deduplication using fields such as:
  - Email
  - ID
  - SKU
  - Other custom columns

- **🔄 Flexible Duplicate Retention**  
  Allows users to configure whether to keep the **first** or **last** occurrence of duplicate records.

- **📊 Multi-Format Enterprise Exporter**  
  Generates unified datasets in:
  - Excel Workbook (`.xlsx`)
  - CSV (`.csv`)
  - Apache Parquet (`.parquet`)

- **🔐 Secure Tokenized Downloads**  
  Generates expiring cryptographic download tokens to protect generated output files.

- **📋 Audit Telemetry**  
  Records processing metrics, skipped files, and merge history through SQLite-based persistence.

- **👤 Authentication & Guest Access**  
  Supports authenticated access while allowing Guest Mode where configured.

- **🐳 Docker-Based Deployment**  
  Includes a production-oriented Docker configuration for cloud deployment platforms such as Render.

- **🎨 Cyberpunk-Inspired Interface**  
  Responsive dashboard with a modern cyberpunk visual style and custom animations.

---

## 🛠️ Technology Stack

| Layer | Technologies |
|---|---|
| **Frontend / Presentation** | PHP, HTML5, CSS3, JavaScript |
| **Backend / Routing** | PHP |
| **Data Processing Engine** | Python 3.10+, Pandas |
| **Spreadsheet Processing** | OpenPyXL |
| **Parquet Export** | PyArrow |
| **Database** | SQLite |
| **Database Access** | PHP PDO |
| **Async Processing** | AJAX |
| **Authentication** | PHP Sessions |
| **Security** | Tokenized Downloads |
| **Containerization** | Docker |
| **Deployment** | Render Web Services |

---

## 🏗️ System Architecture

ExcelMerger Pro follows a **clean-core hybrid architecture**, separating the web application layer from the high-performance data-processing layer.

```text
┌──────────────────────────────────────────────────────────────────┐
│                         Web Application                          │
│                                                                  │
│  ┌───────────────────────┐       ┌────────────────────────────┐  │
│  │     PHP Frontend      │       │       PHP Controllers      │  │
│  │                       │       │                            │  │
│  │  Dashboard UI         │       │  upload.php               │  │
│  │  Login / Auth         │       │  preview_ajax.php         │  │
│  │  Merge Configuration  │       │  download.php             │  │
│  └───────────┬───────────┘       └──────────────┬─────────────┘  │
│              │                                  │                │
│              └──────────────────┬───────────────┘                │
│                                 ▼                                │
│                    ┌────────────────────────┐                    │
│                    │   Python Processing    │                    │
│                    │        Engine         │                    │
│                    │                        │                    │
│                    │  merger.py             │                    │
│                    │  normalizer.py         │                    │
│                    │  deduplicator.py       │                    │
│                    │  exporter.py           │                    │
│                    │  previewer.py          │                    │
│                    └────────────┬───────────┘                    │
│                                 │                                │
│             ┌───────────────────┼───────────────────┐            │
│             ▼                   ▼                   ▼            │
│       SQLite Audit         Master Output       Audit Reports     │
│          Store             XLSX / CSV /       & Metrics         │
│                            Parquet                              │
└──────────────────────────────────────────────────────────────────┘
```

---

## 🔄 End-to-End Workflow

```text
User Login / Guest Mode
          │
          ▼
   Upload ZIP Archive
          │
          ▼
   Pre-Flight Inspection
          │
          ├── Read Spreadsheet Headers
          ├── Detect File Structures
          └── Display Diagnostics
          │
          ▼
   Pipeline Configuration
          │
          ├── Sheet Selection
          ├── Export Format
          ├── Deduplication Mode
          ├── Custom Key Columns
          └── Normalization Rules
          │
          ▼
      merger.py
          │
          ├── Extract Archive
          ├── Validate Files
          ├── Normalize Headers
          ├── Apply Aliases
          ├── Merge DataFrames
          └── Remove Duplicates
          │
          ▼
     Export Dataset
          │
          ├── XLSX
          ├── CSV
          └── Parquet
          │
          ▼
   Audit & Telemetry
          │
          ├── Processing Metrics
          ├── Skipped Files
          ├── Merge History
          └── SQLite Persistence
          │
          ▼
 Secure Tokenized Download
          │
          ▼
      Final Output
```

---

## 📁 Repository Structure

```text
ExcelMerger-Pro/
│
├── config/
│   └── aliases.json
│       # Column mapping and normalization aliases
│
├── database/
│   └── app.db
│       # Runtime SQLite database
│
├── includes/
│   ├── auth.php
│   │   # Session & authentication handler
│   │
│   ├── dashboard_form.php
│   │   # Main dashboard and pipeline controls
│   │
│   ├── db.php
│   │   # PDO database connection
│   │
│   ├── history_panel.php
│   │   # Merge history and telemetry renderer
│   │
│   ├── preview_modal.php
│   │   # Async structural inspection modal
│   │
│   └── security_tokens.php
│       # Expiring download token generation
│
├── outputs/
│   # Generated master datasets
│
├── python_scripts/
│   ├── audit_logger.py
│   │   # Telemetry and skipped-file reporting
│   │
│   ├── deduplicator.py
│   │   # Schema-agnostic row deduplication
│   │
│   ├── exporter.py
│   │   # XLSX, CSV and Parquet exporter
│   │
│   ├── merger.py
│   │   # Core merge pipeline orchestrator
│   │
│   ├── normalizer.py
│   │   # Header normalization
│   │
│   ├── previewer.py
│   │   # Structural analysis helper
│   │
│   ├── requirements.txt
│   │   # Python dependencies
│   │
│   └── sheet_selector.py
│       # Multi-sheet / first-sheet selection
│
├── uploads/
│   # Temporary ZIP upload staging area
│
├── .gitignore
│
├── Dockerfile
│   # Docker / Render deployment configuration
│
├── download.php
│   # Secure token verification and file download
│
├── index.php
│   # Main application shell and router
│
├── login.php
│   # Authentication interface
│
├── logout.php
│   # Session termination
│
├── preview_ajax.php
│   # AJAX structural preview endpoint
│
├── styles.css
│   # Cyberpunk theme and animations
│
├── upload.php
│   # Upload and processing controller
│
├── uploads.ini
│   # PHP upload and memory configuration
│
├── LICENSE
│
└── README.md
```

> **Security Note:** `database/app.db`, uploaded archives, and generated files inside `uploads/` and `outputs/` should normally be treated as runtime data and excluded from Git when they contain real user information or generated datasets.

---

## ⚙️ Getting Started Locally

Follow these steps to set up and run **ExcelMerger Pro** on your local development machine.

### Prerequisites

- **PHP 8.1+**
- **Python 3.10+**
- **pip**
- **Git**
- **PHP PDO SQLite extension**
- A local PHP environment such as:
  - XAMPP
  - WampServer
  - Apache
  - PHP Built-in Server

### 1. Clone the Repository

```bash
git clone https://github.com/twinkleroy139/ExcelMerger-Pro.git
cd ExcelMerger-Pro
```

> If your local folder has a different name, replace `ExcelMerger-Pro` with your actual directory name.

### 2. Install Python Dependencies

Install the required Python packages:

```bash
pip install -r python_scripts/requirements.txt
```

The Python processing layer uses libraries such as:

```text
pandas
openpyxl
pyarrow
```

### 3. Prepare Runtime Directories

Make sure the required runtime directories exist:

```text
uploads/
outputs/
database/
```

If your application automatically creates these directories, this step may not be necessary.

### 4. Start the PHP Application

Run PHP's built-in development server from the project root:

```bash
php -S localhost:8000
```

### 5. Open the Dashboard

Open your browser and navigate to:

```text
http://localhost:8000
```

You can now log in or use Guest Mode if enabled and begin testing the spreadsheet processing workflow.

---

## 🐳 Docker Deployment

ExcelMerger Pro includes a production-oriented **Dockerfile** designed for containerized cloud deployment.

The Docker environment is responsible for:

- Configuring the PHP web server.
- Installing Python and required data-processing libraries.
- Installing spreadsheet and Parquet processing dependencies.
- Preparing application directories.
- Running the application in a cloud-compatible environment.

### ☁️ Deploying to Render

1. Push the project to your GitHub repository.

2. Open the [Render Dashboard](https://dashboard.render.com/).

3. Select:

```text
New + → Web Service
```

4. Connect the repository:

```text
twinkleroy139/ExcelMerger-Pro
```

5. Select:

```text
Environment → Docker
```

6. Allow Render to build the application using the included `Dockerfile`.

7. Configure any required environment variables or persistent storage according to your production configuration.

8. Click:

```text
Create Web Service
```

9. Wait for the Docker image to build and the application to deploy.

### 🌐 Live Production Server

**[https://excelmerger-pro.onrender.com](https://excelmerger-pro.onrender.com)**

---

## 📊 Supported Input & Output Formats

### Input

ExcelMerger Pro can process spreadsheet files including:

```text
.xlsx
.xls
.csv
```

These files can be provided individually or packaged inside a `.zip` archive for batch processing.

### Output

The merged master dataset can be exported as:

```text
.xlsx   → Excel Workbook
.csv    → Comma-Separated Values
.parquet → Apache Parquet
```

---

## 🧩 Header Normalization & Mapping

Different departments may use different names for the same data field.

For example:

```text
Customer Email
customer_email
Email
E-mail
email_address
```

ExcelMerger Pro can normalize these variations and use the `config/aliases.json` mapping configuration to help create a consistent master schema.

### Example

```text
Input Columns
     │
     ├── Customer Email
     ├── customer_id
     ├── Product SKU
     └── Customer Name
     │
     ▼
Normalization
     │
     ▼
Unified Schema
     │
     ├── email
     ├── id
     ├── sku
     └── name
```

---

## 🧹 Deduplication Engine

ExcelMerger Pro supports configurable duplicate detection.

### Full-Row Matching

Compares complete rows:

```text
Row A == Row B
       │
       ▼
   Duplicate
```

### Custom-Key Matching

Users can select specific columns such as:

```text
Email
ID
SKU
```

The system then uses those fields as the deduplication key.

### Retention Rules

```text
Keep First
    │
    └── Preserve the first matching record

Keep Last
    │
    └── Preserve the latest matching record
```

---

## 🔐 Security & Audit

ExcelMerger Pro includes several security-oriented mechanisms:

- Session-based authentication.
- Secure download token generation.
- Expiring download tokens.
- SQLite-based audit persistence.
- File-processing telemetry.
- Skipped-file reporting.
- Input validation during processing.

### Production Security Recommendations

Never commit sensitive runtime data to GitHub.

Add runtime files and generated datasets to `.gitignore` where appropriate:

```gitignore
.env
database/app.db
uploads/*
outputs/*
```

If the application needs empty runtime directories to exist in Git, use a placeholder file:

```text
uploads/.gitkeep
outputs/.gitkeep
database/.gitkeep
```

---

## ⚠️ Important Data Privacy Note

ExcelMerger Pro is designed to process potentially sensitive spreadsheet information.

Before deploying or using the application with real business data:

- Do not expose uploaded files publicly.
- Do not commit uploaded spreadsheets to GitHub.
- Protect generated output files.
- Use secure authentication in production.
- Restrict access to administrative functionality.
- Regularly clean temporary uploads and generated outputs.
- Review your hosting provider's persistent storage and data-retention behavior.
- Avoid storing personally identifiable information unless your deployment is configured appropriately.

---

## ☁️ Live Demo

Experience the production application:

**🚀 [ExcelMerger Pro — Live Application](https://excelmerger-pro.onrender.com)**

---

## 📄 License

This project is distributed under the **MIT License**.

See the [`LICENSE`](LICENSE) file for more information.

---

## 👤 Author

**Twinkle Roy**

Built as a full-stack data-processing and web engineering project combining:

**PHP + Python + Pandas + OpenPyXL + PyArrow + SQLite + Docker**

Designed to demonstrate practical **data engineering, backend development, automation, file processing, security, and cloud deployment** capabilities.

⭐ If you find this project useful, consider giving the repository a star on GitHub.