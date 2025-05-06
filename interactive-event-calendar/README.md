# Interactive Event Calendar

This project is an interactive event calendar that allows users to view and manage events in a user-friendly interface. Users can switch between month, week, and day views, and click on specific dates to see scheduled events.

## Features

- Interactive calendar with month, week, and day views
- Clickable dates to view events
- Detailed event information in a modal
- Ability to add, edit, and delete events

## Project Structure

```
interactive-event-calendar
├── public
│   ├── index.html          # Main HTML document
│   ├── styles              # Directory for CSS styles
│   │   └── main.css        # Main stylesheet
│   └── scripts             # Directory for JavaScript files
│       └── main.js         # Main JavaScript file
├── src
│   ├── components          # Directory for React components
│   │   ├── Calendar.js     # Calendar component
│   │   ├── EventList.js    # Event list component
│   │   └── EventModal.js    # Event modal component
│   ├── services            # Directory for service files
│   │   └── EventService.js # Service for event-related API calls
│   └── utils               # Directory for utility functions
│       └── dateUtils.js    # Date utility functions
├── package.json            # NPM configuration file
├── webpack.config.js       # Webpack configuration file
└── README.md               # Project documentation
```

## Installation

1. Clone the repository:
   ```
   git clone <repository-url>
   ```

2. Navigate to the project directory:
   ```
   cd interactive-event-calendar
   ```

3. Install the dependencies:
   ```
   npm install
   ```

## Usage

To start the development server, run:
```
npm start
```

Open your browser and navigate to `http://localhost:3000` to view the application.

## Contributing

Contributions are welcome! Please open an issue or submit a pull request for any enhancements or bug fixes.

## License

This project is licensed under the MIT License.