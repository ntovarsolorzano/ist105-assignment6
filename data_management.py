#!/usr/bin/env python3
import sys
import json

def process_data(a, b, c, d, e):
    # Store the input values
    values = [a, b, c, d, e]
    original_values = values.copy()
    
    # Check if all inputs are numeric
    try:
        values = [float(val) for val in values]
    except ValueError:
        return json.dumps({
            "error": "Error: All inputs must be numeric values."
        })
    
    # Check for negative values
    negative_values = [val for val in values if val < 0]
    has_negative = len(negative_values) > 0
    
    # Calculate the average
    average = sum(values) / len(values)
    is_avg_greater_than_50 = average > 50
    
    # Count positive numbers
    positive_count = sum(1 for val in values if val > 0)
    
    # Use bitwise operation to check if count is even or odd
    is_even = (positive_count & 1) == 0
    
    # Create a new list with values greater than 10
    values_greater_than_10 = [val for val in values if val > 10]
    sorted_values = sorted(values_greater_than_10)
    
    # Prepare results
    result = {
        "original_values": values,
        "has_negative": has_negative,
        "negative_values": negative_values if has_negative else [],
        "average": average,
        "is_avg_greater_than_50": is_avg_greater_than_50,
        "positive_count": positive_count,
        "is_positive_count_even": is_even,
        "values_greater_than_10": values_greater_than_10,
        "sorted_values": sorted_values
    }
    
    return json.dumps(result)

if __name__ == "__main__":
    # Check if we have enough arguments
    if len(sys.argv) < 6:
        print(json.dumps({"error": "Error: Five numeric values are required."}))
        sys.exit(1)
    
    # Get arguments from command line
    a, b, c, d, e = sys.argv[1:6]
    
    # Process the data
    result = process_data(a, b, c, d, e)
    
    # Print the result for PHP to capture
    print(result)