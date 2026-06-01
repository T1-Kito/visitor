param(
    [Parameter(Mandatory = $true)]
    [string] $PayloadPath,

    [Parameter(Mandatory = $true)]
    [string] $PrinterName,

    [string] $Paper = "80mm"
)

$ErrorActionPreference = "Stop"

Add-Type -TypeDefinition @"
using System;
using System.Runtime.InteropServices;

public class RawPrinterHelper
{
    [StructLayout(LayoutKind.Sequential, CharSet = CharSet.Ansi)]
    public class DOCINFOA
    {
        [MarshalAs(UnmanagedType.LPStr)] public string pDocName;
        [MarshalAs(UnmanagedType.LPStr)] public string pOutputFile;
        [MarshalAs(UnmanagedType.LPStr)] public string pDataType;
    }

    [DllImport("winspool.Drv", EntryPoint = "OpenPrinterA", SetLastError = true, CharSet = CharSet.Ansi, ExactSpelling = true, CallingConvention = CallingConvention.StdCall)]
    public static extern bool OpenPrinter(string szPrinter, out IntPtr hPrinter, IntPtr pd);

    [DllImport("winspool.Drv", EntryPoint = "ClosePrinter", SetLastError = true, ExactSpelling = true, CallingConvention = CallingConvention.StdCall)]
    public static extern bool ClosePrinter(IntPtr hPrinter);

    [DllImport("winspool.Drv", EntryPoint = "StartDocPrinterA", SetLastError = true, CharSet = CharSet.Ansi, ExactSpelling = true, CallingConvention = CallingConvention.StdCall)]
    public static extern bool StartDocPrinter(IntPtr hPrinter, Int32 level, [In, MarshalAs(UnmanagedType.LPStruct)] DOCINFOA di);

    [DllImport("winspool.Drv", EntryPoint = "EndDocPrinter", SetLastError = true, ExactSpelling = true, CallingConvention = CallingConvention.StdCall)]
    public static extern bool EndDocPrinter(IntPtr hPrinter);

    [DllImport("winspool.Drv", EntryPoint = "StartPagePrinter", SetLastError = true, ExactSpelling = true, CallingConvention = CallingConvention.StdCall)]
    public static extern bool StartPagePrinter(IntPtr hPrinter);

    [DllImport("winspool.Drv", EntryPoint = "EndPagePrinter", SetLastError = true, ExactSpelling = true, CallingConvention = CallingConvention.StdCall)]
    public static extern bool EndPagePrinter(IntPtr hPrinter);

    [DllImport("winspool.Drv", EntryPoint = "WritePrinter", SetLastError = true, ExactSpelling = true, CallingConvention = CallingConvention.StdCall)]
    public static extern bool WritePrinter(IntPtr hPrinter, byte[] pBytes, Int32 dwCount, out Int32 dwWritten);

    public static bool SendBytesToPrinter(string printerName, byte[] bytes)
    {
        IntPtr hPrinter;
        DOCINFOA di = new DOCINFOA();
        di.pDocName = "Gatehouse QR Ticket";
        di.pDataType = "RAW";

        if (!OpenPrinter(printerName.Normalize(), out hPrinter, IntPtr.Zero)) return false;

        try
        {
            if (!StartDocPrinter(hPrinter, 1, di)) return false;
            if (!StartPagePrinter(hPrinter)) return false;
            int written;
            bool ok = WritePrinter(hPrinter, bytes, bytes.Length, out written);
            EndPagePrinter(hPrinter);
            EndDocPrinter(hPrinter);
            return ok && written == bytes.Length;
        }
        finally
        {
            ClosePrinter(hPrinter);
        }
    }
}
"@

function Remove-VietnameseMarks([string] $Text) {
    if ([string]::IsNullOrWhiteSpace($Text)) {
        return "-"
    }

    $normalized = $Text.Normalize([Text.NormalizationForm]::FormD)
    $builder = New-Object Text.StringBuilder
    foreach ($char in $normalized.ToCharArray()) {
        $category = [Globalization.CharUnicodeInfo]::GetUnicodeCategory($char)
        if ($category -ne [Globalization.UnicodeCategory]::NonSpacingMark) {
            [void] $builder.Append($char)
        }
    }

    return $builder.ToString().Normalize([Text.NormalizationForm]::FormC).
        Replace("đ", "d").
        Replace("Đ", "D")
}

function Add-Bytes([System.Collections.Generic.List[byte]] $Buffer, [byte[]] $Bytes) {
    foreach ($byte in $Bytes) {
        $Buffer.Add($byte)
    }
}

function Add-Text([System.Collections.Generic.List[byte]] $Buffer, [string] $Text) {
    $safeText = Remove-VietnameseMarks $Text
    Add-Bytes $Buffer ([Text.Encoding]::ASCII.GetBytes($safeText + "`n"))
}

function Add-Center([System.Collections.Generic.List[byte]] $Buffer) {
    Add-Bytes $Buffer ([byte[]](0x1B, 0x61, 0x01))
}

function Add-Left([System.Collections.Generic.List[byte]] $Buffer) {
    Add-Bytes $Buffer ([byte[]](0x1B, 0x61, 0x00))
}

function Add-Bold([System.Collections.Generic.List[byte]] $Buffer, [bool] $Enabled) {
    Add-Bytes $Buffer ([byte[]](0x1B, 0x45, [byte]([int]$Enabled)))
}

function Add-Size([System.Collections.Generic.List[byte]] $Buffer, [byte] $Size) {
    Add-Bytes $Buffer ([byte[]](0x1D, 0x21, $Size))
}

function Add-Separator([System.Collections.Generic.List[byte]] $Buffer, [int] $Width) {
    Add-Text $Buffer ("-" * $Width)
}

function Add-Row([System.Collections.Generic.List[byte]] $Buffer, [string] $Label, [string] $Value, [int] $Width) {
    $labelSafe = Remove-VietnameseMarks $Label
    $valueSafe = Remove-VietnameseMarks $Value
    $maxValue = [Math]::Max(8, $Width - $labelSafe.Length - 1)
    if ($valueSafe.Length -gt $maxValue) {
        $valueSafe = $valueSafe.Substring(0, $maxValue)
    }
    $spaces = [Math]::Max(1, $Width - $labelSafe.Length - $valueSafe.Length)
    Add-Text $Buffer ($labelSafe + (" " * $spaces) + $valueSafe)
}

function Add-Qr([System.Collections.Generic.List[byte]] $Buffer, [string] $Data) {
    $dataBytes = [Text.Encoding]::ASCII.GetBytes($Data)
    $length = $dataBytes.Length + 3
    $pL = [byte]($length % 256)
    $pH = [byte][Math]::Floor($length / 256)

    # ESC/POS QR: model 2, module size 6, correction M, store, print.
    Add-Bytes $Buffer ([byte[]](0x1D, 0x28, 0x6B, 0x04, 0x00, 0x31, 0x41, 0x32, 0x00))
    Add-Bytes $Buffer ([byte[]](0x1D, 0x28, 0x6B, 0x03, 0x00, 0x31, 0x43, 0x06))
    Add-Bytes $Buffer ([byte[]](0x1D, 0x28, 0x6B, 0x03, 0x00, 0x31, 0x45, 0x31))
    Add-Bytes $Buffer ([byte[]](0x1D, 0x28, 0x6B, $pL, $pH, 0x31, 0x50, 0x30))
    Add-Bytes $Buffer $dataBytes
    Add-Bytes $Buffer ([byte[]](0x1D, 0x28, 0x6B, 0x03, 0x00, 0x31, 0x51, 0x30))
}

$payload = Get-Content -LiteralPath $PayloadPath -Raw | ConvertFrom-Json
$width = if ($Paper -eq "58mm") { 32 } else { 42 }
$qrData = if ($payload.qrToken) { [string] $payload.qrToken } elseif ($payload.code) { [string] $payload.code } else { "-" }

$buffer = New-Object 'System.Collections.Generic.List[byte]'

# Init printer.
Add-Bytes $buffer ([byte[]](0x1B, 0x40))
Add-Center $buffer
Add-Bold $buffer $true
Add-Size $buffer 0x11
Add-Text $buffer "GATEHOUSE PRO"
Add-Size $buffer 0x00
Add-Text $buffer "PHIEU MA QR"
Add-Bold $buffer $false
Add-Text $buffer ""
Add-Qr $buffer $qrData
Add-Text $buffer ""
Add-Bold $buffer $true
Add-Text $buffer ([string] $payload.code)
Add-Bold $buffer $false
Add-Text $buffer ""
Add-Left $buffer
Add-Separator $buffer $width
Add-Row $buffer "Khach" ([string] $payload.visitorName) $width
Add-Row $buffer "Cong ty" ([string] $payload.visitorCompany) $width
Add-Row $buffer "Nguoi tiep" ([string] $payload.hostName) $width
Add-Row $buffer "Gio hen" ([string] $payload.scheduledAt) $width
Add-Row $buffer "Trang thai" ([string] $payload.status) $width
Add-Separator $buffer $width
Add-Center $buffer
Add-Text $buffer "Khong chia se ma QR cho nguoi khac."
Add-Text $buffer ""
Add-Text $buffer ""

# Feed and cut.
Add-Bytes $buffer ([byte[]](0x1B, 0x64, 0x03))
Add-Bytes $buffer ([byte[]](0x1D, 0x56, 0x42, 0x00))

$ok = [RawPrinterHelper]::SendBytesToPrinter($PrinterName, $buffer.ToArray())
if (-not $ok) {
    throw "Khong gui duoc len may in '$PrinterName'. Kiem tra ten may in va driver."
}

Write-Output "Printed to $PrinterName"
