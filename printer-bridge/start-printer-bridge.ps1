$ErrorActionPreference = "Stop"

$BridgeDir = Split-Path -Parent $MyInvocation.MyCommand.Path
$ConfigFile = Join-Path $BridgeDir "config.json"
$ExampleConfigFile = Join-Path $BridgeDir "config.example.json"

if (-not (Test-Path -LiteralPath $ConfigFile)) {
    Copy-Item -LiteralPath $ExampleConfigFile -Destination $ConfigFile
}

Set-Location $BridgeDir
node server.js
