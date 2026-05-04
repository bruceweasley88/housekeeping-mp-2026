## ADDED Requirements

### Requirement: User verify page has community dropdown filter
The admin user verify (业主认证) page SHALL display a community dropdown selector in the search form area. The dropdown SHALL load all communities from the existing `/community.community/lists` API and display community names as options.

#### Scenario: Community dropdown loads on page mount
- **WHEN** admin user opens the user verify page
- **THEN** the community dropdown SHALL be populated with all available communities from the API

#### Scenario: Select a community filters verify list
- **WHEN** admin selects a specific community from the dropdown
- **THEN** the verify list SHALL be filtered to show only verification records for that community
- **AND** the total count SHALL reflect the filtered verification count for that community

#### Scenario: Clear community selection restores full list
- **WHEN** admin clears the community dropdown selection
- **THEN** the verify list SHALL return to showing all verification records without community filter

#### Scenario: Community filter combines with other filters
- **WHEN** admin selects a community AND sets status filter to "待审核"
- **THEN** the verify list SHALL show only pending verifications for that specific community
