import json
import mysql.connector
from flask import Flask, request, jsonify
from flask_cors import CORS

# --- 1. SETUP ---
app = Flask(__name__)
# This is CRITICAL. It allows your PHP server (e.g., http://localhost)
# to send requests to this Python server (http://localhost:5000).
CORS(app)

# --- 2. DATABASE CONFIGURATION ---
# !!! CHANGE THESE to match your XAMPP/MySQL database credentials !!!
DB_CONFIG = {
    'user': 'root',               # Your MySQL username (default is often 'root')
    'password': '',               # Your MySQL password (default is often empty)
    'host': 'localhost',          # or 'localhost'  127.0.0.1
    'database': 'raltt_db'    # The name of your database
}

# --- 3. CREATE THE API ROUTE ---
# This is the new URL your JavaScript will send data to.
@app.route('/save_preferences', methods=['POST'])
def save_preferences():
    """
    Receives user_id and a list of 3 design preferences.
    Deletes any old preferences and saves the new ones with a rank.
    """
    try:
        # --- 4. GET DATA FROM THE REQUEST ---
        # We use request.form because the JS sends 'application/x-www-form-urlencoded'
        categories_json = request.form.get('categories')
        user_id = request.form.get('user_id')

        # Check if we got the data we need
        if not user_id or not categories_json:
            return jsonify({'success': False, 'message': 'Missing user_id or categories'}), 400

        # --- 5. PARSE THE DATA ---
        # Convert the JSON string "['minimalist','floral','modern']"
        # into a Python list ['minimalist', 'floral', 'modern']
        categories = json.loads(categories_json)
        user_id = int(user_id) # Convert user_id to an integer

        if len(categories) != 3:
            return jsonify({'success': False, 'message': 'Must select exactly 3 categories'}), 400

        # --- 6. CONNECT TO DATABASE AND SAVE ---
        # We use a 'with' block for cleaner connection handling
        with mysql.connector.connect(**DB_CONFIG) as conn:
            with conn.cursor() as cursor:

                # First, clear any old preferences this user might have
                cursor.execute("DELETE FROM user_design_preferences WHERE user_id = %s", (user_id,))

                # Prepare the SQL to insert the new preferences
                # Your table needs columns: user_id, design_id, rank
                sql = "INSERT INTO user_design_preferences (user_id, design_id, rank) VALUES (%s, %s, %s)"

                # Create a list of tuples to insert
                # (user_id, 'minimalist', 1)
                # (user_id, 'floral', 2)
                # (user_id, 'modern', 3)
                values_to_insert = []
                for rank, design_id in enumerate(categories):
                    # rank starts at 0, so we add 1
                    values_to_insert.append((user_id, design_id, rank + 1)) 

                # Execute the query for all 3 preferences at once
                cursor.executemany(sql, values_to_insert)

                # Commit the changes to the database
                conn.commit()

        # --- 7. SEND SUCCESS RESPONSE ---
        return jsonify({'success': True, 'message': 'Preferences saved successfully!'})

    except mysql.connector.Error as err:
        # Handle database-specific errors
        return jsonify({'success': False, 'message': f'Database error: {err}'}), 500
    except json.JSONDecodeError:
        # Handle error if 'categories' is not valid JSON
        return jsonify({'success': False, 'message': 'Invalid categories format. Must be a JSON array.'}), 400
    except Exception as e:
        # Handle all other errors
        return jsonify({'success': False, 'message': f'An error occurred: {e}'}), 500

# --- 8. RUN THE APP ---
if __name__ == '__main__':
    # This starts the server
    # debug=True automatically reloads the server when you save changes
    app.run(debug=True, port=5000)