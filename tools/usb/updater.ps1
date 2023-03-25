Add-Type -assembly System.Windows.Forms
$main_form = New-Object System.Windows.Forms.Form
$main_form.Text ='Alexandria mirror updater'
$main_form.Width = 800
$main_form.Height = 600
$main_form.AutoSize = $true
$main_form.ShowDialog()
