#!/usr/bin/env python3
import sys
from pdf2docx import Converter

def convert_pdf_to_docx(pdf_path, docx_path):
    cv = Converter(pdf_path)
    cv.convert(docx_path, start=0, end=None)
    cv.close()

if __name__ == '__main__':
    if len(sys.argv) != 3:
        print('Usage: convert.py <input.pdf> <output.docx>')
        sys.exit(1)
    pdf_file = sys.argv[1]
    docx_file = sys.argv[2]
    try:
        convert_pdf_to_docx(pdf_file, docx_file)
    except Exception as e:
        print('Error:', e)
        sys.exit(2)
