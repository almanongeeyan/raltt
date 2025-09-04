# Phone Verification System with Twilio

This project now includes a complete phone verification system using Twilio's Verify service for user registration.

## Features

- **Phone Number Validation**: Ensures Philippine mobile number format (+639XXXXXXXXX)
- **SMS Verification**: Sends 6-digit verification codes via SMS
- **Rate Limiting**: Prevents abuse with IP-based rate limiting
- **Security**: Enhanced error handling and logging
- **User Experience**: Real-time feedback and validation

## Files Modified/Created

### 1. `connection/send_verification.php`
- Sends verification codes via Twilio SMS
- Includes rate limiting (5 attempts per 5 minutes per IP)
- Enhanced error handling and logging
- CORS support for cross-origin requests

### 2. `connection/check_verification.php`
- Verifies 6-digit codes entered by users
- Rate limiting for verification attempts (10 attempts per 5 minutes per IP)
- Comprehensive error handling for different verification states

### 3. `register.php`
- Updated JavaScript to call actual Twilio APIs
- Real-time phone verification integration
- Enhanced user feedback and validation

### 4. `connection/test_twilio.php`
- Test file to verify Twilio integration
- **Remove this file in production**

## Twilio Credentials Used

```php
Account SID: AC4ce3b22a6b0813ddabc8af53330f2b63
Auth Token: 6ac768c348b35d8b86f276612c2fca8f
Verify Service SID: VA332fb460c09cf1680db23118718cad64
```

## How It Works

### 1. User Registration Flow
1. User enters phone number in format +639XXXXXXXXX
2. Clicks "Verify" button
3. System sends SMS with 6-digit code via Twilio
4. User enters the code received
5. System verifies code with Twilio
6. If successful, phone number is marked as verified
7. User can complete registration

### 2. Security Features
- **Rate Limiting**: Prevents spam and abuse
- **Input Validation**: Server-side validation of phone numbers and codes
- **Error Logging**: All verification attempts are logged
- **CORS Protection**: Proper headers for cross-origin requests

### 3. Error Handling
- Invalid phone number format
- Twilio API errors (authentication, quota, etc.)
- Network issues
- Rate limit exceeded

## Testing

1. **Test Twilio Integration**: Visit `connection/test_twilio.php` to verify credentials
2. **Test Registration**: Use the registration form with a valid Philippine mobile number
3. **Check Logs**: Monitor `database_errors.log` for any issues

## Requirements

- PHP 7.4+
- Composer with Twilio SDK
- Valid Twilio account with Verify service enabled
- SMS credits in Twilio account

## Installation

1. Ensure Composer dependencies are installed:
   ```bash
   composer install
   ```

2. Verify Twilio credentials are correct in the verification files

3. Test the integration using `connection/test_twilio.php`

4. Remove test file before going to production

## Usage in Registration

The phone verification is automatically integrated into the registration form:

- Users must verify their phone number before registration
- Verified phone numbers are stored in a hidden field
- Registration process only proceeds with verified numbers
- Database stores the verified phone number

## Troubleshooting

### Common Issues

1. **"Twilio SDK not found"**
   - Run `composer install` to install dependencies

2. **"Authentication failed"**
   - Check Twilio credentials in verification files

3. **"Service not found"**
   - Verify the Verify Service SID is correct
   - Ensure Verify service is enabled in Twilio console

4. **"Quota exceeded"**
   - Check SMS credits in Twilio account
   - Monitor usage in Twilio console

### Logs

Check these locations for error information:
- `database_errors.log` - Database and general errors
- PHP error logs - Twilio API errors
- Browser console - JavaScript errors

## Security Notes

- **Remove test files** before production deployment
- **Monitor logs** for suspicious activity
- **Consider environment variables** for Twilio credentials in production
- **Implement additional rate limiting** if needed for high-traffic sites

## Support

For Twilio-specific issues, refer to:
- [Twilio Verify Documentation](https://www.twilio.com/docs/verify)
- [Twilio PHP SDK Documentation](https://www.twilio.com/docs/libraries/php)
- [Twilio Console](https://console.twilio.com/)

For application-specific issues, check the logs and ensure all dependencies are properly installed.
