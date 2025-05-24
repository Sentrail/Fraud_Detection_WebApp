import pandas as pd
import pickle
import sys

# Load trained model
model_path = "../data/model.pkl"
with open(model_path, "rb") as f:
    model = pickle.load(f)

# Load cleaned dataset
data_path = "../data/cleaned_data.csv"
df = pd.read_csv(data_path)

# Predict fraud
df["fraud_prediction"] = model.predict(df.drop(columns=["Is_Fraud"]))  # Drop actual fraud labels

# Save the results
output_path = "../data/fraud_predictions.csv"
df.to_csv(output_path, index=False)

print("\n✅ Fraud detection completed! Results saved.")
