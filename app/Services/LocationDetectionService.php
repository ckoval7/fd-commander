<?php

namespace App\Services;

/**
 * Service for detecting user location and presence type.
 *
 * Handles IP subnet matching, coordinate distance calculations,
 * and suggesting presence type based on network location.
 */
class LocationDetectionService
{
    /**
     * Earth's radius in meters for Haversine formula.
     */
    private const EARTH_RADIUS_METERS = 6371000;

    /**
     * Check if an IP address matches any of the given CIDR subnets.
     *
     * Supports both IPv4 and IPv6 addresses. Returns false for invalid
     * IP addresses or empty subnet arrays.
     *
     * @param  string  $ip  The IP address to check
     * @param  array<int, string>  $subnets  Array of CIDR notation subnets (e.g., ['192.168.1.0/24', '10.0.0.0/8'])
     * @return bool True if IP matches any subnet, false otherwise
     */
    public function isLocalNetwork(string $ip, array $subnets): bool
    {
        if (empty($subnets) || filter_var($ip, FILTER_VALIDATE_IP) === false) {
            return false;
        }

        foreach ($subnets as $subnet) {
            if ($this->ipInSubnet($ip, $subnet)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Calculate distance between two coordinate points using Haversine formula.
     *
     * Returns the great-circle distance between two points on Earth's surface
     * specified by latitude and longitude coordinates.
     *
     * @param  float  $lat1  Latitude of first point in decimal degrees
     * @param  float  $lon1  Longitude of first point in decimal degrees
     * @param  float  $lat2  Latitude of second point in decimal degrees
     * @param  float  $lon2  Longitude of second point in decimal degrees
     * @return float Distance in meters
     */
    public function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        // Convert degrees to radians
        $lat1Rad = deg2rad($lat1);
        $lon1Rad = deg2rad($lon1);
        $lat2Rad = deg2rad($lat2);
        $lon2Rad = deg2rad($lon2);

        // Calculate differences
        $deltaLat = $lat2Rad - $lat1Rad;
        $deltaLon = $lon2Rad - $lon1Rad;

        // Haversine formula
        $a = sin($deltaLat / 2) ** 2 +
             cos($lat1Rad) * cos($lat2Rad) *
             sin($deltaLon / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        // Distance in meters
        return self::EARTH_RADIUS_METERS * $c;
    }

    /**
     * Suggest presence type based on IP subnet matching.
     *
     * This is the fallback method when browser geolocation is unavailable.
     * Returns 'in_person' if the IP matches any local subnet, otherwise 'online'.
     *
     * Note: Coordinate parameters are kept for interface consistency but are not
     * used in this fallback method. Browser-side geolocation handles coordinate
     * validation directly.
     *
     * @param  float|null  $eventLat  Event latitude (not used in IP-based detection)
     * @param  float|null  $eventLon  Event longitude (not used in IP-based detection)
     * @param  int|null  $radius  Event radius in meters (not used in IP-based detection)
     * @param  string  $ip  User's IP address
     * @param  array<int, string>  $subnets  Array of CIDR notation subnets
     * @return string Either 'in_person' or 'online'
     */
    public function suggestPresenceType(
        ?float $eventLat,
        ?float $eventLon,
        ?int $radius,
        string $ip,
        array $subnets
    ): string {
        // If subnets are provided and IP matches, consider in-person
        if (! empty($subnets) && $this->isLocalNetwork($ip, $subnets)) {
            return 'in_person';
        }

        // Default to online
        return 'online';
    }

    /**
     * Check if user coordinates are within event radius.
     *
     * Helper method to determine if a user's location falls within
     * the specified radius of an event location.
     *
     * @param  float  $userLat  User's latitude
     * @param  float  $userLon  User's longitude
     * @param  float  $eventLat  Event's latitude
     * @param  float  $eventLon  Event's longitude
     * @param  int  $radiusMeters  Radius in meters
     * @return bool True if within radius, false otherwise
     */
    public function isWithinRadius(
        float $userLat,
        float $userLon,
        float $eventLat,
        float $eventLon,
        int $radiusMeters
    ): bool {
        $distance = $this->calculateDistance($userLat, $userLon, $eventLat, $eventLon);

        return $distance <= $radiusMeters;
    }

    /**
     * Check if an IP address is within a CIDR subnet.
     *
     * Supports both IPv4 and IPv6 CIDR notation.
     *
     * @param  string  $ip  IP address to check
     * @param  string  $subnet  CIDR notation subnet (e.g., '192.168.1.0/24')
     * @return bool True if IP is in subnet, false otherwise
     */
    private function ipInSubnet(string $ip, string $subnet): bool
    {
        if (! str_contains($subnet, '/')) {
            return false;
        }

        [$subnetIp, $prefixLength] = explode('/', $subnet, 2);

        if (! is_numeric($prefixLength) || filter_var($subnetIp, FILTER_VALIDATE_IP) === false) {
            return false;
        }

        $prefixLength = (int) $prefixLength;
        $isIpv6 = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false;
        $isSubnetIpv6 = filter_var($subnetIp, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false;

        // IP version mismatch
        if ($isIpv6 !== $isSubnetIpv6) {
            return false;
        }

        return $isIpv6
            ? $this->ipv6InSubnet($ip, $subnetIp, $prefixLength)
            : $this->ipv4InSubnet($ip, $subnetIp, $prefixLength);
    }

    /**
     * Check if an IPv4 address is within a subnet.
     *
     * @param  string  $ip  IPv4 address
     * @param  string  $subnetIp  Subnet IPv4 address
     * @param  int  $prefixLength  CIDR prefix length (0-32)
     * @return bool True if IP is in subnet, false otherwise
     */
    private function ipv4InSubnet(string $ip, string $subnetIp, int $prefixLength): bool
    {
        // Validate prefix length for IPv4
        if ($prefixLength < 0 || $prefixLength > 32) {
            return false;
        }

        // Convert IPs to long integers
        $ipLong = ip2long($ip);
        $subnetLong = ip2long($subnetIp);

        if ($ipLong === false || $subnetLong === false) {
            return false;
        }

        // Calculate network mask
        $mask = -1 << (32 - $prefixLength);

        // Compare network portions
        return ($ipLong & $mask) === ($subnetLong & $mask);
    }

    /**
     * Check if an IPv6 address is within a subnet.
     *
     * @param  string  $ip  IPv6 address
     * @param  string  $subnetIp  Subnet IPv6 address
     * @param  int  $prefixLength  CIDR prefix length (0-128)
     * @return bool True if IP is in subnet, false otherwise
     */
    private function ipv6InSubnet(string $ip, string $subnetIp, int $prefixLength): bool
    {
        if ($prefixLength < 0 || $prefixLength > 128) {
            return false;
        }

        $ipBinary = inet_pton($ip);
        $subnetBinary = inet_pton($subnetIp);

        if ($ipBinary === false || $subnetBinary === false) {
            return false;
        }

        $bytesToCompare = intdiv($prefixLength, 8);
        $remainingBits = $prefixLength % 8;

        // Compare full bytes of the prefix
        if ($bytesToCompare > 0 && substr($ipBinary, 0, $bytesToCompare) !== substr($subnetBinary, 0, $bytesToCompare)) {
            return false;
        }

        // Compare remaining bits if any
        if ($remainingBits > 0) {
            $mask = (0xFF << (8 - $remainingBits)) & 0xFF;

            return (ord($ipBinary[$bytesToCompare]) & $mask) === (ord($subnetBinary[$bytesToCompare]) & $mask);
        }

        return true;
    }
}
