# OEM_toners
Search a printer model and get standard OEM toner info (cost, color, yeld, etc.)

# Terms of Reference

Using a product name (e.g. HP LaserJet Pro M404 or HP Color LaserJet CP3525) determine the following at Staples (Staples is a popular reseller of OEM toner and they maintain competitive prices):
 - Cost of Standard OEM Black toner ("Standard" meaning not high yield; often there is also pricing for High Yield toner cartridges)
 - Yield (number of expected print pages)
 - if the device is color, then we also need the same information for the cyan, yellow, and magenta toner cartridges. The cost and the yield for these 3 colors are almost always identical.

Actual examples for the devices listed above are shown below; data for these devices was obtained from Staples earlier today. This is exactly what we need and only looking at Staples is just fine. This gives us a reasonable estimate to know what the customer's current spend is. (Occasionally we will get what they actually spend, but that is rare.)

HP LaserJet Pro M404 (monochrome printer)
 - Standard Yield Toner Cost: $105.99
 - Black toner Yield: 3,000 impressions

HP Color LaserJet CP3525 (color printer):
 - Black Toner standard Yield: $158.89
 - Black toner Yield: 5,000 impressions
 - Cyan, Yellow, and Magenta standard yield cartridge cost: $311.89 (each color costs the same)
 - Color cartridge yield: 7,000 impressions (each color has the same yield)

# Setup
 - composer install
 - php artisan migrate
 - php artisan db:seed
 - php artisan serve

# List of printer models for testing:
- Brother HL-3170CDW
- Brother HL-4150CDN
- Brother HL-L3230CDW
- Brother HL-L3270CDW
- Brother HL-L3290CDW
- Brother HL-L8260CDW
- Brother HL-L8360CDW
- Brother MFC-9340CDW
- Brother MFC-J1010DW
- Brother MFC-J6920DW
- Brother MFC-L3710CW
- Brother MFC-L8900CDW
- Brother MFC-L9570CDW
- Canon PIXMA G6020
- Canon PIXMA TS205
- Canon PIXMA TS3150
- Epson EcoTank ET-2800
- Epson Stylus Pro 3850
- Fargo DTC 1250E Printer
- HP CF210A
- HP CF350A
- HP Color LaserJet CP3525
- HP Color LaserJet CP4525dn
- HP Color LaserJet Enterprise M553dn
- HP Color LaserJet Pro CM1415fnw MFP
- HP Color LaserJet Pro M252dw
- HP Color LaserJet Pro M254dw
- HP Color LaserJet Pro M452dn
- HP Color LaserJet Pro M452dw
- HP Color LaserJet Pro M454dn
- HP Color LaserJet Pro M454dw
- HP Color LaserJet Pro MFP M277dw
- HP Color LaserJet Pro MFP M281fdw
- HP Color LaserJet Pro MFP M477fdn
- HP Color LaserJet Pro MFP M477fdw
- HP Color LaserJet Pro MFP M477fnw
- HP Color LaserJet Pro MFP M479fdn
- HP Color LaserJet Pro MFP M479fdw
- HP DeskJet 2755e
- HP DeskJet 3630
- HP ENVY Inspire 7955e
- HP LaserJet 4101 MFPDUPLEX
- HP LaserJet Managed MFP E42540f
- HP LaserJet Managed MFP E72535dn
- HP LaserJet Pro 300 color MFP M375nw
- HP LaserJet Pro 400 M401dn
- HP LaserJet Pro 400 M401dw
- HP LaserJet Pro 400 MFP M425dn
- HP LaserJet Pro 400 color M451dn
- HP LaserJet Pro 400 color M451nw
- HP LaserJet Pro M404
- HP LaserJet Pro MFP 3101
- HP OfficeJet 3830
- HP OfficeJet 4500 All in one
- HP OfficeJet 4650
- HP OfficeJet Pro 6230
- HP OfficeJet Pro 6968 All-in-One
- HP OfficeJet Pro 6978 All-in-One
- HP OfficeJet Pro 7740
- HP OfficeJet Pro 8210
- HP OfficeJet Pro 8600
- HP OfficeJet Pro 8610
- HP OfficeJet Pro 8620
- HP OfficeJet Pro 8710
- HP OfficeJet Pro 8720
- HP PhotoSmart 5510 e-All in one
- HP Photosmart C4272
- Kyocera EcoSys
- Lexmark CX 417de
- Lexmark CX725de
- OKI C330dn
- Ricoh MP 401SPF
- Samsung ProXpress C2620 DW
- Xerox C315
