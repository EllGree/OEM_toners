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

# List of printer models for testing:
 - HP LaserJet Pro M404
 - HP LaserJet Pro MFP 3101
 - HP ENVY Inspire 7955e
 - HP Color LaserJet CP3525
 - HP Photosmart C4272
 - HP DeskJet 2755e
 - Epson EcoTank ET-2800
 - Brother MFC-J1010DW
 - Canon PIXMA G6020
 - Fargo DTC 1250E Printer
 - Lexmark CX725de
 - Kyocera EcoSys
 - Samsung ProXpress C2620 DW
 - Xerox C315
 - OKI C330dn
