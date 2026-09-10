$root = "C:\Users\alex\Desktop\VS CODE PROJECTS\Maison"
$names = @("about","the-house","products","find","nearby")
$created = @()

foreach ($n in $names) {
  $src = Join-Path $root "$n.html"
  $dst = Join-Path $root "gr\$n.html"

  if (-not (Test-Path $dst)) {
    Copy-Item -Path $src -Destination $dst
    $created += $dst
  }
}

foreach ($file in $created) {
  $content = Get-Content -Raw -Path $file
  $content = $content.Replace('<html lang="en">','<html lang="el">')
  $content = $content.Replace('href="media/logo/bebop-favicon.ico"','href="../media/logo/bebop-favicon.ico"')
  $content = $content.Replace('href="styles.css"','href="../styles.css"')
  $content = $content.Replace('src="components.js"','src="../components.js"')
  $content = $content.Replace('src="media/','src="../media/')
  $content = $content.Replace("url('media/","url('../media/")
  $content = $content -replace '#SITE_URL#/(about|the-house|products|find|nearby)\.html','#SITE_URL#/gr/$1.html'
  $content = $content.Replace('content="en_US"','content="el_GR"')

  Set-Content -Path $file -Value $content -Encoding utf8
}

Write-Host "Created files: $($created.Count)"
$created
