--Formateo
diskutil eraseDisk MS-DOS "WIN11" MBR /dev/disk4
--Monstar ISO
hdiutil mount ~/Downloads/windows11.iso
--Copiar Archivos ISO
rsync -vha --exclude=sources/install.wim /Volumes/CCCOMA_X64FRE_ES-MX_DV9/* /Volumes/WIN11
-Copiar Archivo ISO
mkdir /Volumes/WIN11/sources/
wimlib-imagex split /Volumes/CCCOMA_X64FRE_ES-MX_DV9/sources/install.wim /Volumes/WIN11/sources/install.swm 3800
--Desmontar ISO
diskutil unmount /Volumes/WIN11
diskutil unmount /Volumes/CCCOMA_X64FRE_ES-MX_DV9



--Comando para correr deepseek usando docker
docker run -d -p 3000:8080 --add-host=host.docker.internal:host-gatwey -v open-webui:/app/backend/data --name open-webui --restart always ghcr.io/open-webui/open-webui:main