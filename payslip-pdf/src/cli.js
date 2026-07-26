#!/usr/bin/env node
import fs from 'node:fs/promises';
import path from 'node:path';
import process from 'node:process';
import { generatePayslipPdf, payslipFilename } from './generate-payslip-pdf.js';

const [inputFile, outputFile] = process.argv.slice(2);
if (!inputFile) {
  console.error('Usage: npm run generate -- <payroll.json> [output.pdf]');
  process.exitCode = 1;
} else {
  try {
    const data = JSON.parse(await fs.readFile(path.resolve(inputFile), 'utf8'));
    const outputPath = outputFile || path.join('output', payslipFilename(data));
    console.log(await generatePayslipPdf(data, { outputPath }));
  } catch (error) {
    console.error(`Payslip generation failed: ${error.message}`);
    process.exitCode = 1;
  }
}
