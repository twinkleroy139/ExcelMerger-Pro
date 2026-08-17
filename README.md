# 🚀 ExcelMerger Pro v2.6

ExcelMerger Pro is a high-performance batch processing web application designed to merge thousands of individual Excel sheets from a single ZIP archive, align headers, filter duplicates, and compile a clean master spreadsheet.

It features a **Cyberpunk Diagnostic UI**, user-isolated local history tracking via **SQLite**, a public feedback terminal, and a secure Python/Pandas backend parsing engine.

---

## ✨ Key Features
* **High-Speed Batch Processing:** Powered by Python (`pandas` & `openpyxl`) to handle large datasets effortlessly.
* **Cyberpunk Terminal Loader:** Interactive, real-time diagnostic progress animation with dynamic speed and chunk monitoring.
* **User-Isolated Storage:** Logged-in users have private access to their execution history logs, while guest users can process files safely in session mode without leaving server footprints.
* **Feedback & Rating Terminal:** Integrated comment box and star-rating system to capture user notes and contact emails securely.
* **Zero Leak Security:** Configured with robust `.gitignore` rules to keep database files, active uploads, and private logs away from public version control.

---

## 🛠️ Tech Stack
* **Frontend:** HTML5, Tailwind CSS, Custom CSS (Scanline animations)
* **Backend:** PHP (Session management, routing, database handlers)
* **Database:** SQLite (Serverless, zero-configuration local data store)
* **Processing Engine:** Python 3 + Pandas + Openpyxl

---

## ⚙️ Installation & Local Setup

1. **Clone the repository:**
   ```bash
   git clone [https://github.com/twinkleroy139/ExcelMerger-Pro.git](https://github.com/twinkleroy139/ExcelMerger-Pro.git)
   cd ExcelMerger-Pro