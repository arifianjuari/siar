#!/bin/bash
find app/Http/Controllers/Modules/RiskManagement app/Models resources/views/modules/RiskManagement resources/views/risk_reports -type f -name "*.php" -o -name "*.blade.php" | xargs grep -l "riskreport_number\|risk_title"
