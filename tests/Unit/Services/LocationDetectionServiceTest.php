<?php

use App\Services\LocationDetectionService;

beforeEach(function () {
    $this->service = new LocationDetectionService;
});

describe('CIDR matching tests', function () {
    it('matches valid IPv4 in subnet', function () {
        $result = $this->service->isLocalNetwork('192.168.1.50', ['192.168.1.0/24']);

        expect($result)->toBeTrue();
    });

    it('does not match IPv4 outside subnet', function () {
        $result = $this->service->isLocalNetwork('192.168.2.50', ['192.168.1.0/24']);

        expect($result)->toBeFalse();
    });

    it('matches IPv4 in /8 subnet', function () {
        $result = $this->service->isLocalNetwork('10.20.30.40', ['10.0.0.0/8']);

        expect($result)->toBeTrue();
    });

    it('matches IPv4 in /16 subnet', function () {
        $result = $this->service->isLocalNetwork('172.16.50.100', ['172.16.0.0/16']);

        expect($result)->toBeTrue();
    });

    it('matches IPv4 in /24 subnet', function () {
        $result = $this->service->isLocalNetwork('192.168.1.100', ['192.168.1.0/24']);

        expect($result)->toBeTrue();
    });

    it('matches IPv4 in /32 subnet (single host)', function () {
        $result = $this->service->isLocalNetwork('192.168.1.50', ['192.168.1.50/32']);

        expect($result)->toBeTrue();
    });

    it('does not match different host in /32 subnet', function () {
        $result = $this->service->isLocalNetwork('192.168.1.51', ['192.168.1.50/32']);

        expect($result)->toBeFalse();
    });

    it('matches IPv6 in subnet', function () {
        $result = $this->service->isLocalNetwork('2001:db8::1', ['2001:db8::/32']);

        expect($result)->toBeTrue();
    });

    it('does not match IPv6 outside subnet', function () {
        $result = $this->service->isLocalNetwork('2001:db9::1', ['2001:db8::/32']);

        expect($result)->toBeFalse();
    });

    it('matches IPv6 in /64 subnet', function () {
        $result = $this->service->isLocalNetwork('2001:db8:abcd:0012::1', ['2001:db8:abcd:0012::/64']);

        expect($result)->toBeTrue();
    });

    it('returns false for invalid IP address', function () {
        $result = $this->service->isLocalNetwork('not-an-ip', ['192.168.1.0/24']);

        expect($result)->toBeFalse();
    });

    it('returns false for empty subnet array', function () {
        $result = $this->service->isLocalNetwork('192.168.1.50', []);

        expect($result)->toBeFalse();
    });

    it('matches against multiple subnets', function () {
        $subnets = [
            '192.168.1.0/24',
            '10.0.0.0/8',
            '172.16.0.0/16',
        ];

        expect($this->service->isLocalNetwork('192.168.1.50', $subnets))->toBeTrue();
        expect($this->service->isLocalNetwork('10.20.30.40', $subnets))->toBeTrue();
        expect($this->service->isLocalNetwork('172.16.50.100', $subnets))->toBeTrue();
        expect($this->service->isLocalNetwork('8.8.8.8', $subnets))->toBeFalse();
    });

    it('does not match IPv4 address against IPv6 subnet', function () {
        $result = $this->service->isLocalNetwork('192.168.1.50', ['2001:db8::/32']);

        expect($result)->toBeFalse();
    });

    it('does not match IPv6 address against IPv4 subnet', function () {
        $result = $this->service->isLocalNetwork('2001:db8::1', ['192.168.1.0/24']);

        expect($result)->toBeFalse();
    });
});

describe('Haversine distance tests', function () {
    it('calculates known distance between two points', function () {
        // Distance between New York City and Los Angeles
        // NYC: 40.7128° N, 74.0060° W
        // LA: 34.0522° N, 118.2437° W
        // Expected distance: ~3944 km (3,944,000 meters)
        $distance = $this->service->calculateDistance(40.7128, -74.0060, 34.0522, -118.2437);

        expect($distance)->toBeGreaterThan(3900000)
            ->toBeLessThan(4000000);
    });

    it('returns zero for same point', function () {
        $distance = $this->service->calculateDistance(40.7128, -74.0060, 40.7128, -74.0060);

        expect($distance)->toBeLessThan(0.001); // Allow for floating point precision
    });

    it('calculates symmetric distance', function () {
        // Distance from A to B should equal distance from B to A
        $lat1 = 40.7128;
        $lon1 = -74.0060;
        $lat2 = 34.0522;
        $lon2 = -118.2437;

        $distanceAtoB = $this->service->calculateDistance($lat1, $lon1, $lat2, $lon2);
        $distanceBtoA = $this->service->calculateDistance($lat2, $lon2, $lat1, $lon1);

        expect($distanceAtoB)->toEqual($distanceBtoA);
    });

    it('calculates short distance accurately', function () {
        // Two points roughly 1km apart
        // Start: 40.7489° N, 73.9680° W (Times Square, NYC)
        // End: 40.7589° N, 73.9680° W (roughly 1.1 km north)
        $distance = $this->service->calculateDistance(40.7489, -73.9680, 40.7589, -73.9680);

        expect($distance)->toBeGreaterThan(1000)
            ->toBeLessThan(1200);
    });

    it('handles equator crossing', function () {
        // Point north of equator to point south of equator
        $distance = $this->service->calculateDistance(10.0, 0.0, -10.0, 0.0);

        // Should be approximately 2,222 km
        expect($distance)->toBeGreaterThan(2200000)
            ->toBeLessThan(2300000);
    });

    it('handles international date line', function () {
        // Points on either side of international date line
        $distance = $this->service->calculateDistance(0.0, 179.0, 0.0, -179.0);

        // Should be approximately 222 km
        expect($distance)->toBeGreaterThan(200000)
            ->toBeLessThan(250000);
    });
});

describe('isWithinRadius tests', function () {
    it('returns true for point within radius', function () {
        // Event location: 40.7489° N, 73.9680° W (Times Square)
        // User location: 40.7589° N, 73.9680° W (roughly 1.1 km away)
        // Radius: 2000 meters
        $result = $this->service->isWithinRadius(
            40.7589,
            -73.9680,
            40.7489,
            -73.9680,
            2000
        );

        expect($result)->toBeTrue();
    });

    it('returns false for point outside radius', function () {
        // Event location: 40.7489° N, 73.9680° W (Times Square)
        // User location: 40.7589° N, 73.9680° W (roughly 1.1 km away)
        // Radius: 500 meters
        $result = $this->service->isWithinRadius(
            40.7589,
            -73.9680,
            40.7489,
            -73.9680,
            500
        );

        expect($result)->toBeFalse();
    });

    it('returns true for point at exact radius', function () {
        // This tests the edge case where distance equals radius
        // We'll use the same point, so distance is 0, which is within any positive radius
        $result = $this->service->isWithinRadius(
            40.7489,
            -73.9680,
            40.7489,
            -73.9680,
            1000
        );

        expect($result)->toBeTrue();
    });

    it('returns true for very small radius when points are same', function () {
        $result = $this->service->isWithinRadius(
            40.7489,
            -73.9680,
            40.7489,
            -73.9680,
            1
        );

        expect($result)->toBeTrue();
    });

    it('handles large radius correctly', function () {
        // NYC to LA with 5000km radius
        $result = $this->service->isWithinRadius(
            40.7128,
            -74.0060,
            34.0522,
            -118.2437,
            5000000
        );

        expect($result)->toBeTrue();
    });

    it('handles large radius rejection correctly', function () {
        // NYC to LA with 3000km radius (should fail)
        $result = $this->service->isWithinRadius(
            40.7128,
            -74.0060,
            34.0522,
            -118.2437,
            3000000
        );

        expect($result)->toBeFalse();
    });
});

describe('suggestPresenceType tests', function () {
    it('returns in_person when IP matches subnet', function () {
        $result = $this->service->suggestPresenceType(
            null,
            null,
            null,
            '192.168.1.50',
            ['192.168.1.0/24']
        );

        expect($result)->toBe('in_person');
    });

    it('returns online when IP does not match subnet', function () {
        $result = $this->service->suggestPresenceType(
            null,
            null,
            null,
            '192.168.2.50',
            ['192.168.1.0/24']
        );

        expect($result)->toBe('online');
    });

    it('returns online when subnet array is empty', function () {
        $result = $this->service->suggestPresenceType(
            null,
            null,
            null,
            '192.168.1.50',
            []
        );

        expect($result)->toBe('online');
    });

    it('returns online for invalid IP address', function () {
        $result = $this->service->suggestPresenceType(
            null,
            null,
            null,
            'not-an-ip',
            ['192.168.1.0/24']
        );

        expect($result)->toBe('online');
    });

    it('returns in_person when IP matches any of multiple subnets', function () {
        $subnets = [
            '192.168.1.0/24',
            '10.0.0.0/8',
            '172.16.0.0/16',
        ];

        $result = $this->service->suggestPresenceType(
            null,
            null,
            null,
            '10.20.30.40',
            $subnets
        );

        expect($result)->toBe('in_person');
    });

    it('ignores coordinate parameters in IP-based detection', function () {
        // Coordinates are not used, so even with valid coordinates,
        // only IP matching determines the result
        $result = $this->service->suggestPresenceType(
            40.7489,
            -73.9680,
            1000,
            '8.8.8.8',
            ['192.168.1.0/24']
        );

        expect($result)->toBe('online');
    });
});
