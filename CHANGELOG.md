# Changelog

All notable changes to this project will be documented in this file.

## [Unreleased] - 2026-03-15

### Added

#### Market Service
- **Spot Symbols Endpoint Update**: Updated `getSpotSymbols()` to use `/openApi/spot/v1/common/symbols` endpoint
  - Added support for `maxMarketNotional` field (maximum notional amount for single market order)
  - Added new symbol status value `29 = Pre-Delisted`
  - Full status values: 0=Offline, 1=Online, 5=Pre-open, 10=Accessed, 25=Suspended, 29=Pre-Delisted, 30=Delisted

- **Spot Klines v2 Endpoint**: Updated `getSpotKlines()` to use `/openApi/spot/v2/market/kline`
  - Added optional `timeZone` parameter (0=UTC (default), 8=UTC+8)
  - Updated max limit from 1000 to 1440 records

#### Spot Account Service
- **Internal Transfer Update**: Updated `internalTransfer()` method with new parameters
  - Changed `walletType` to integer: 1=Fund Account, 2=Standard Futures, 3=Perpetual Futures, **4=Spot Account** (NEW)
  - Added `userAccountType` parameter (1=UID, 2=Phone number, 3=Email)
  - Added `userAccount` parameter
  - Added optional `callingCode` parameter (required when userAccountType=2)
  - Added optional `transferClientId` parameter (custom ID, max 100 chars)
  - Added optional `recvWindow` parameter

#### Sub-Account Service
- **Sub-Account Internal Transfer Update**: Updated `subAccountInternalTransfer()` with new parameters
  - Changed `walletType` to integer: 1=Fund Account, 2=Standard Futures, 3=Perpetual Futures, **15=Spot Account** (NEW)
  - Added `userAccountType` parameter (1=UID, 2=Phone number, 3=Email)
  - Added `userAccount` parameter
  - Added optional `callingCode` parameter
  - Added optional `transferClientId` parameter
  - Added optional `recvWindow` parameter

- **New Method**: `subMotherAccountAssetTransfer()` - Sub-Mother Account Asset Transfer Interface
  - Flexible asset transfer between parent and sub-accounts
  - Supports account types: 1=Funding, 2=Standard futures, 3=Perpetual U-based, 15=Spot
  - Only available to master account
  - Returns `tranId` (transfer record ID)

- **New Method**: `getSubMotherAccountTransferableAmount()` - Query Sub-Mother Account Transferable Amount
  - Query supported coins and available transferable amounts
  - Only available to master account
  - Returns coins array with id, name, and availableAmount

- **New Method**: `getSubMotherAccountTransferHistory()` - Query Sub-Mother Account Transfer History
  - Query transfer history between sub-accounts and parent account
  - Supports filtering by type, tranId, time range
  - Pagination support (pageId, pagingSize)
  - Only available to master account

### Changed
- Updated BingX API integration to support changes from December 2025 through February 2026
- Improved parameter validation and type safety across all updated methods

### API Compatibility
- All changes maintain backward compatibility where possible
- New optional parameters added with sensible defaults
- Existing method signatures preserved for non-breaking changes

---

## API Reference
For detailed API documentation, see: https://bingx-api.github.io/docs-v3/
