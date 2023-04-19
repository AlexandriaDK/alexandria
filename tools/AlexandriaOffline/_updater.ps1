$version = 1.0;
$client = "usb2023"
$fileDownloadLimit = 10

$exporturl = "https://alexandria.dk/export?client=$client&version=$version"
$versionurl = $exporturl + "&newestversion=powershellupdater"
$staticurl = "https://loot.alexandria.dk/AlexandriaOffline/data/alexandria_content.js"
$scriptstaticurl = "https://loot.alexandria.dk/AlexandriaOffline/_updater.ps1"
$contentFilename = "$PSScriptRoot\data\alexandria_content.js"
$json = ""

Add-Type -AssemblyName System.Windows.Forms,PresentationFramework
$form = New-Object System.Windows.Forms.Form
$form.Text ='Alexandria Downloader'
$form.Width = 600
$form.Height = 400
$form.AutoSize = $true
$form.StartPosition = 'CenterScreen'

# Icon as Base64
$iconBase64      = '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'
$iconBytes       = [Convert]::FromBase64String($iconBase64)
$stream          = [System.IO.MemoryStream]::new($iconBytes, 0, $iconBytes.Length)
$form.Icon       = [System.Drawing.Icon]::FromHandle(([System.Drawing.Bitmap]::new($stream).GetHIcon()))

$header = New-Object System.Windows.Forms.Label
$header.Text = "Alexandria Downloader"
$header.Location  = New-Object System.Drawing.Point(10,10)
$header.AutoSize = $true
$header.Font = New-Object System.Drawing.Font ("Arial", 30)
$form.Controls.Add($header)

$description = New-Object System.Windows.Forms.Label
$description.Text = "Update local content from Alexandria.dk"
$description.Location  = New-Object System.Drawing.Point(15,70)
$description.AutoSize = $true
$description.Font = New-Object System.Drawing.Font ("Arial", 12)
$form.Controls.Add($description)

# Links
$LinkLabel = New-Object System.Windows.Forms.LinkLabel
$LinkLabel.Location  = New-Object System.Drawing.Point(15,($form.Height - 65))
$LinkLabel.Size = New-Object System.Drawing.Size(200,20)
$LinkLabel.LinkColor = "BLUE"
$LinkLabel.ActiveLinkColor = "RED"
$LinkLabel.Text = "Open offline copy on this computer"
$LinkLabel.add_Click({[system.Diagnostics.Process]::start("$PSScriptRoot\index.html")})
$Form.Controls.Add($LinkLabel)

$LinkLabel2 = New-Object System.Windows.Forms.LinkLabel
$LinkLabel2.Location  = New-Object System.Drawing.Point(($form.Width - 200),($form.Height - 65))
$LinkLabel2.Size = New-Object System.Drawing.Size(180,20)
$LinkLabel2.LinkColor = "BLUE"
$LinkLabel2.ActiveLinkColor = "RED"
$LinkLabel2.Text = "Visit Alexandria USB project page"
$LinkLabel2.add_Click({[system.Diagnostics.Process]::start("https://alexandria.dk/usb?client=$client&version=$version")})
$Form.Controls.Add($LinkLabel2)

# Action buttons
function updateJSON($json) {
    if (-not $toJS) {
        $text = $json
    } else {
        $text = "function loadAlexandria() {`r`ndata = " + $json + "`r`n}`r`n"
    }
    $filename = $contentFilename
    updateStatus("Filename: $filename")
    $directory = [IO.Path]::GetDirectoryName($filename)
    if (-not [IO.Directory]::Exists($directory)) {
        updateStatus("Creating ""Data"" Folder")
        [IO.Directory]::CreateDirectory($directory)
    }
    [IO.File]::WriteAllLines($filename, $text)
}

function updateStatus($text) {
    $status.AppendText("[" + (Get-Date -UFormat "%T") + "] " + $text + "`r`n")
}

function startupAction {
    updateStatus("Alexandria Downloader version $version.")
    updateStatus("Checking Alexandria export service.")
    $export = ""
    $export = Invoke-WebRequest "$versionurl" -TimeoutSec 10 -UseBasicParsing
    if (-not $export.StatusCode) {
        updateStatus("Service is unavailable. Please try again later.")
        return
    }
    $result = ConvertFrom-Json $export
    $newestversion = $result.result.version
    if ($newestversion -gt $version) {
        $doUpdate = [System.Windows.MessageBox]::Show(
            "A newer version of the update script is available.`r`nDownload newest version?`r`n`r`n(newest version: $newestversion; local version: $version)",
            "Update available",
            "YesNo",
            "Question"
        )
        if ($doUpdate -eq 'Yes') {
            updateStatus("Foo $doUpdate")
            $outFile = $PSScriptRoot + "\" + '_updater.ps1'
            Invoke-WebRequest -Uri $scriptstaticurl -Outfile $outFile -UseBasicParsing
            [System.Windows.MessageBox]::Show(
                "Download complete. Please restart the script.",
                "Download complete",
                "OK",
                "Information"
            )
            Exit
        }
    }
    $length = $False
    $length = (Invoke-WebRequest $staticurl -TimeoutSec 5 -UseBasicParsing -Method Head).Headers.'Content-Length'
    if (-not $length) {
        updateStatus("Database file is unavailable. Please try again later.")
        return
    }
    updateStatus("Size of online database file: About " + [Math]::Round($length/1024/1024) + " MB.")

    updateStatus("Ready for update.")
}

function updateAction() {
    # Could use better error handling and progress

    # Fetching cached copy (updated every night)
    # Press Ctrl when clicking to request live copy
    $live = [System.Windows.Forms.Control]::ModifierKeys -band [System.Windows.Forms.Keys]::Control

    if ($live) {
        $toJS = $True
        $exporturldata = $exporturl + '&dataset=all'
        updateStatus("Fetching LIVE data - hang on. This can take several minutes.")
    } else {
        $toJS = $False
        $exporturldata = $staticurl
        updateStatus("Fetching data - hang on. This can take several minutes.")
    }
    $export = $False
    $export = Invoke-WebRequest "$exporturldata" -TimeoutSec 300 -UseBasicParsing

    if (-not $export) {
        updateStatus("Error fetching content. Please try again later.")
        return $False
    }

    updateStatus("Download complete! Length: " + ($export.Content.Length/1MB).ToString(".0") + " MB.")
    updateStatus("Validating download.")
    $json = $export.Content
    if ($json.Length -lt 100000) { # Basic check if content is too small
        updateStatus("Error: Content is incomplete. Please try again later.")
        return $False
    }
    if ($live) {
        try {
            $json | ConvertFrom-Json
        } catch {
            updateStatus("Error: Content is invalid JSON. Please try again later.")
            return $False
        }
    }
    updateStatus("Saving content.")
    updateJSON($json)
    updateStatus("Done!")
    return
}

function createFilesFolders {
    ('', 'conset','convent','issue','person','scenario','system','tag') | ForEach-Object {
        $folder = ("files/" + $_)
        if (-not (Test-Path -Path ("$PSScriptRoot\$folder") -PathType Container) ) {
            updateStatus "Creating folder $folder"
            New-Item -Path $PSScriptRoot -Name $folder -ItemType Directory
        }
    }
}
function getPathFromFileData {
    $filename = ""
    $path = 'files/'
    if ($_.game_id) {
        $path = $path + 'scenario/' + $_.game_id + "/"
    } elseif ($_.convention_id) {
        $path = $path + 'convent/' + $_.convention_id + "/"
    } elseif ($_.conset_id) {
        $path = $path + 'conset/' + $_.conset_id + "/"
    } elseif ($_.issue_id) {
        $path = $path + 'issue/' + $_.issue_id + "/"
    } elseif ($_.person_id) {
        $path = $path + 'person/' + $_.person_id + "/"
    } elseif ($_.gamesystem_id) {
        $path = $path + 'system/' + $_.gamesystem_id + "/"
    } elseif ($_.tag_id) {
        $path = $path + 'tag/' + $_.tag_id + "/"
    } else {
        $global:filename = ""
        return $False
    }
    if (-not (Test-Path -Path ("$PSScriptRoot\$path") -PathType Container) ) {
        New-Item -Path $PSScriptRoot -Name $path -ItemType Directory
    }
    $filename = $path + $_.filename
    $global:filename = $filename
    return $filename
}

function filesAction {
    updateStatus("Checking database.") 
    if (-not (Test-Path -Path $contentFilename -PathType Leaf) ) {
        updateStatus("Error: Database file does not exist. Run ""Check for updates"".")
        return $False
    }
    createFilesFolders
    $json = $global:json
    if (-not $json) {
        updateStatus("Loading database - hang on. This can take several seconds.")
        $json = (Get-Content $contentFilename)[1].substring(7) | ConvertFrom-JSON # Assume JSON position ...
    }
    $global:json = $json
    $totalCount = ($json.result.files).count
    updateStatus("Found " + ($json.result.files).count + " files in database.")
    updateStatus("Checking existing files.")
    $missingFiles = @()
    $existingCount = 0
    $checkedCount = 0
    $json.result.files | ForEach-Object {
        getPathFromFileData($_.filename, $_.game_id, $_.convention_id, $_.conset_id, $_.gamesystem_id, $_.tag_id, $_.issue_id)
        $filename = $global:filename
        if ($filename) {
            updateStatus("Checking $filename")
            if (Test-Path -Path ("$PSScriptRoot\$filename") -PathType Leaf) {
                $existingCount = $existingCount + 1
            } else {
                updateStatus("Adding $filename")
                $missingFiles += $filename
            }
        }
        $checkedCount = $checkedCount + 1
        if ($checkedCount % 100 -eq 0) {
            updateStatus("Checked $checkedCount files (" + [Math]::Round($checkedCount/$totalCount*100) + "%)")
        }
    }
    if ($checkedCount % 100 -ne 0) {
        updateStatus("Checked $checkedCount files (" + [Math]::Round($checkedCount/$totalCount*100) + "%)")
    }
    updateStatus ("Existing files: $existingCount")
    updateStatus ("Missing files: " + $missingFiles.count)
    updateStatus ("Total count: " + $totalCount)
    $missingFiles

    if ($missingFiles.count -eq 0) {
        updateStatus "No downloaded needed!"
        return $True
    }
    updateStatus "Beginning download. Be very patient!"
    if ($missingFiles > $fileDownloadLimit) {
        updateStatus "(Limit: Only downloading first $fileDownloadLimit files)"
    }
    updateStatus ""
    $downloadCount = 0
    $missingFiles | Select-Object -first $fileDownloadLimit | ForEach-Object {
        $downloadCount += 1
        $outFile = $PSScriptRoot + "\" + $_
        $uri = 'https://download.alexandria.dk/' + $_
        updateStatus("Downloading $downloadCount of " + $missingFiles.count + ": $uri")
        Invoke-WebRequest -Uri $uri -Outfile $outFile -UseBasicParsing
    }
    updateStatus "Done!"
    return $True
}

$updateClick = {
    $updateButton.Enabled = $false
    $filesButton.Enabled = $false
    updateAction
    $updateButton.Enabled = $true
    $filesButton.Enabled = $true
}

$filesClick = {
    $updateButton.Enabled = $false
    $filesButton.Enabled = $false
    filesAction
    $updateButton.Enabled = $true
    $filesButton.Enabled = $true
}

$updateButton = New-Object System.Windows.Forms.Button 
$updateButton.Location = New-Object System.Drawing.Point(15,120) 
$updateButton.Size = New-Object System.Drawing.Size(200,60)
$updateButton.Text = 'Update database'
$updateButton.Font = New-Object System.Drawing.Font ("Arial", 15)
$updateButton.add_Click($updateClick)
$form.Controls.Add($updateButton)

$filesButton = New-Object System.Windows.Forms.Button 
$filesButton.Location = New-Object System.Drawing.Point(360,120) 
$filesButton.Size = New-Object System.Drawing.Size(200,60)
$filesButton.Text = 'Download files'
$filesButton.Font = New-Object System.Drawing.Font ("Arial", 15)
$filesButton.add_Click($filesClick)
$form.Controls.Add($filesButton)

# Status textbox
$status = New-Object System.Windows.Forms.TextBox 
$status.Multiline = $True;
$status.Location = New-Object System.Drawing.Size(15,200) 
$status.Size = New-Object System.Drawing.Size(($form.Width - 40),130)
$status.Scrollbars = "Vertical" 
$status.Enabled = $true
$form.Controls.Add($status)

startupAction

# Start up dialog
$result = $form.ShowDialog()
