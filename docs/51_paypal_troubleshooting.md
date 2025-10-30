# PayPal Troubleshooting Guide

## Common Error: INVALID_PARAMETER_SYNTAX

### Symptom
PayPal payments fail with error:
```
INVALID_PARAMETER_SYNTAX: The value of a field does not conform to the expected format
```

### Root Cause
PayPal's Orders API v2 requires strict data formatting for all fields. Common issues include:

1. **Phone numbers** not in E.164 format
2. **Email addresses** with invalid format
3. **Country codes** not using 2-letter ISO format
4. **Negative or improperly formatted amounts**

### Solution (Fixed in Warehouse 2)
The Warehouse add-on now includes automatic validation and formatting for all PayPal fields:

#### Phone Number Formatting
Phone numbers are automatically converted to E.164 format:
- German format: `0123 456789` → `+49123456789`
- International: `0049 123 456789` → `+49123456789`
- Already formatted: `+49 123 456789` → `+49123456789`

**Supported countries**: DE, AT, CH, FR, IT, ES, GB, US, CA, NL, BE, PL, CZ, DK, SE, NO, FI, GR, PT, IE

Invalid phone numbers are automatically skipped (not sent to PayPal).

#### Email Validation
Email addresses are validated using PHP's `filter_var()` function before being sent to PayPal.

#### Country Code Validation
Country codes are validated to ensure they are exactly 2 letters (ISO 3166-1 alpha-2 format).
Invalid codes fall back to the store's default country code.

#### Amount Validation
All monetary amounts are validated to be:
- Positive (>= 0)
- Properly formatted as decimal numbers
- At least 1 for item quantities

### For Developers

#### Extending Country Support
To add support for additional countries, edit `lib/Api/Order.php` and add entries to the `COUNTRY_DIAL_CODES` constant:

```php
private const COUNTRY_DIAL_CODES = [
    'DE' => '49',  // Germany
    'XX' => 'YYY', // Your Country
    // ...
];
```

#### Testing Phone Number Formatting
Test your phone numbers before submitting orders:
1. Use the E.164 format: `+[country code][number]`
2. Ensure 7-15 digits total (excluding the + sign)
3. Remove all spaces, dashes, and parentheses

#### Best Practices
1. Store phone numbers with country codes in your database
2. Validate customer data at the point of entry (during checkout)
3. Display formatted phone numbers to users for verification
4. Use 2-letter ISO country codes consistently

### Related Documentation
- [PayPal Orders API v2](https://developer.paypal.com/docs/api/orders/v2/)
- [E.164 Phone Number Format](https://en.wikipedia.org/wiki/E.164)
- [ISO 3166-1 alpha-2 Country Codes](https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2)
