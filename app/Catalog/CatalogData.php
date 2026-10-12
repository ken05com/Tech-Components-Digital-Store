<?php

namespace App\Catalog;

/**
 * The seed catalog, transcribed verbatim from
 * "the subtopis and samples.docx" (Shoby Keneth R. Daamo, BSIT - 3A).
 *
 * Nothing in here may be reworded, re-ordered or re-priced: this file is the
 * single source of truth for the whole application.
 */
final class CatalogData
{
    /**
     * Rows are [category, id, name, description, priceMin, priceMax, image, variants].
     * Variants are catalogue labels only — no invented prices — shown as a
     * dropdown on the storefront product page.
     *
     * @return array<int, array{0: Category, 1: int, 2: string, 3: string, 4: int, 5: int, 6: string, 7: array<int, string>}>
     */
    private const ROWS = [
        // Subtopic 1 - Computer Components
        [Category::ComputerComponents, 1, 'CPU / Processor', 'Central processing unit that executes computer instructions.', 5000, 10000, 'cpu-processor.png', ['Intel Core i3', 'Intel Core i5', 'Intel Core i7', 'AMD Ryzen 5', 'AMD Ryzen 7']],
        [Category::ComputerComponents, 2, 'Motherboard', 'Main circuit board that connects computer components.', 5000, 7000, 'motherboard.jpeg', ['ATX', 'Micro-ATX', 'Mini-ITX']],
        [Category::ComputerComponents, 3, 'RAM', 'Temporary memory used by the computer to run programs.', 4500, 6000, 'ram.jpeg', ['4GB DDR4', '8GB DDR4', '16GB DDR4', '32GB DDR4']],
        [Category::ComputerComponents, 4, 'GPU', 'Processes graphics and visual information for display.', 10000, 50000, 'gpu.jpeg', ['4GB VRAM', '8GB VRAM', '12GB VRAM', '16GB VRAM']],
        [Category::ComputerComponents, 5, 'PSU', 'Supplies electrical power to computer components.', 5000, 5000, 'psu.png', ['500W', '650W', '750W', '850W']],

        // Subtopic 2 - Storage Devices
        [Category::StorageDevices, 1, 'HDD', 'Magnetic storage device used to save files and data.', 2500, 5000, 'hdd.jpeg', ['500GB', '1TB', '2TB', '4TB']],
        [Category::StorageDevices, 2, 'SSD', 'Fast storage device that uses flash memory.', 5500, 10000, 'ssd.png', ['240GB', '480GB', '1TB', '2TB']],
        [Category::StorageDevices, 3, 'NVMe SSD', 'High-speed solid-state storage using the NVMe protocol.', 6000, 15000, 'nvme-ssd.jpeg', ['500GB', '1TB', '2TB']],
        [Category::StorageDevices, 4, 'USB Flash Drive', 'Portable device used to store and transfer digital files.', 1500, 2000, 'usb-flash-drive.jpeg', ['16GB', '32GB', '64GB', '128GB']],
        [Category::StorageDevices, 5, 'Memory Card', 'Small storage device used in phones, cameras, and other devices.', 800, 1500, 'memory-card.jpeg', ['16GB', '32GB', '64GB', '128GB']],

        // Subtopic 3 - Computer Accessories
        [Category::ComputerAccessories, 1, 'Keyboard', 'Input device used for typing and controlling a computer.', 500, 2000, 'keyboard.jpeg', ['Membrane', 'Mechanical', 'Wireless']],
        [Category::ComputerAccessories, 2, 'Mouse', 'Pointing device used to navigate and interact with a computer.', 300, 1500, 'mouse.png', ['Wired', 'Wireless', 'Gaming']],
        [Category::ComputerAccessories, 3, 'Webcam', 'Camera used for video calls, meetings, and streaming.', 800, 3000, 'webcam.jpeg', ['720p HD', '1080p Full HD', '4K Ultra HD']],
        [Category::ComputerAccessories, 4, 'Speakers', 'Audio output device used to play sound from a computer.', 500, 3000, 'speakers.jpeg', ['2.0 Stereo', '2.1 Subwoofer', 'Bluetooth']],
        [Category::ComputerAccessories, 5, 'Headset', 'Audio device with headphones and a microphone.', 500, 2500, 'headset.png', ['Wired', 'Wireless', 'Noise-cancelling']],

        // Subtopic 4 - Networking Components
        [Category::NetworkingComponents, 1, 'Wi-Fi Router', 'Network device that provides wireless connectivity.', 1000, 5000, 'wi-fi-router.jpeg', ['Wi-Fi 5', 'Wi-Fi 6', 'Wi-Fi 6E', 'Dual-band']],
        [Category::NetworkingComponents, 2, 'Network Switch', 'Connects multiple devices within a local area network.', 500, 3000, 'network-switch.jpeg', ['5-Port', '8-Port', '16-Port', '24-Port']],
        [Category::NetworkingComponents, 3, 'Ethernet Cable', 'Connects network devices through wired communication.', 100, 500, 'ethernet-cable.jpeg', ['Cat5e', 'Cat6', 'Cat6a']],
        [Category::NetworkingComponents, 4, 'Wi-Fi Adapter', 'Allows a computer to connect to a wireless network.', 300, 1500, 'wi-fi-adapter.png', ['USB', 'PCIe']],
        [Category::NetworkingComponents, 5, 'LAN Card', 'Network interface device for wired connectivity.', 300, 1500, 'lan-card.jpeg', ['100Mbps', 'Gigabit', '2.5G']],

        // Subtopic 5 - Power & Connection
        [Category::PowerAndConnection, 1, 'HDMI Cable', 'Transmits digital audio and video between devices.', 150, 800, 'hdmi-cable.jpeg', ['1m', '2m', '3m', '5m']],
        [Category::PowerAndConnection, 2, 'USB Cable', 'Used for data transfer and charging electronic devices.', 100, 500, 'usb-cable.jpeg', ['USB-A', 'USB-C', 'Micro USB']],
        [Category::PowerAndConnection, 3, 'Power Cable', 'Supplies electrical power to computers and electronic devices.', 150, 500, 'power-cable.jpeg', ['1m', '2m', '3m']],
        [Category::PowerAndConnection, 4, 'USB Hub', 'Expands the number of available USB ports.', 300, 1500, 'usb-hub.jpeg', ['4-Port', '7-Port', 'USB 3.0']],
        [Category::PowerAndConnection, 5, 'Surge Protector', 'Helps protect electronic equipment from voltage surges.', 500, 2000, 'surge-protector.jpeg', ['4-Outlet', '6-Outlet', '8-Outlet']],
    ];

    /**
     * The 25 products exactly as they appear in the document.
     *
     * @return array<int, Product>
     */
    public static function products(): array
    {
        static $products = null;

        if ($products !== null) {
            return $products;
        }

        $base = strtotime('2026-09-21 06:45:00') ?: 0;
        $products = [];

        foreach (self::ROWS as $index => [$category, $id, $name, $description, $min, $max, $image, $variants]) {
            $products[] = new Product(
                id: $index + 1,
                categoryId: $id,
                category: $category,
                name: $name,
                description: $description,
                priceMin: $min,
                priceMax: $max,
                image: $image,
                variants: $variants,
                createdAt: date('Y-m-d H:i:s', $base + ($index * 3600)),
                updatedAt: date('Y-m-d H:i:s', $base + ($index * 3600)),
            );
        }

        return $products;
    }

    public static function total(): int
    {
        return count(self::products());
    }
}
