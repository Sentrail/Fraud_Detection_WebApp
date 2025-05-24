import pandas as pd
import numpy as np
import seaborn as sns
import matplotlib.pyplot as plt
from sklearn.preprocessing import LabelEncoder, MinMaxScaler

# 📌 Load the dataset
file_path = "Bank_Transaction_Fraud_Detection.csv"  # Change this to your actual file path
df = pd.read_csv(file_path, encoding="ISO-8859-1")

# 📌 Display initial info
print("\n🔍 Original Dataset Info:")
print(df.info())

# -----------------------------------------------
# 🚀 Step 1: Handle Missing Values
# -----------------------------------------------
missing_values = df.isnull().sum()
print("\n🔍 Missing Values Before Processing:\n", missing_values)

# Fill missing values (example: fill missing balances with the median)
df["Account_Balance"].fillna(df["Account_Balance"].median(), inplace=True)

# Drop columns with too many missing values (if applicable)
df.dropna(axis=1, how="all", inplace=True)

# -----------------------------------------------
# 🚀 Step 2: Convert Date & Time Columns
# -----------------------------------------------
df["Transaction_Date"] = pd.to_datetime(df["Transaction_Date"], errors="coerce", format="%d-%m-%Y")
df["Transaction_Time"] = pd.to_datetime(df["Transaction_Time"], errors="coerce", format="%H:%M:%S").dt.time

# Extract new features from date
df["Transaction_Day"] = df["Transaction_Date"].dt.day
df["Transaction_Month"] = df["Transaction_Date"].dt.month
df["Transaction_Year"] = df["Transaction_Date"].dt.year

# -----------------------------------------------
# 🚀 Step 3: Encode Categorical Variables
# -----------------------------------------------
categorical_cols = ["Gender", "Account_Type", "Transaction_Type", "Merchant_Category", "Transaction_Device", "Device_Type", "Transaction_Currency"]

# Apply Label Encoding
label_encoders = {}
for col in categorical_cols:
    le = LabelEncoder()
    df[col] = le.fit_transform(df[col])
    label_encoders[col] = le  # Save encoders for later use

# -----------------------------------------------
# 🚀 Step 4: Normalize Numerical Features
# -----------------------------------------------
scaler = MinMaxScaler()
df[["Transaction_Amount", "Account_Balance"]] = scaler.fit_transform(df[["Transaction_Amount", "Account_Balance"]])

# -----------------------------------------------
# 🚀 Step 5: Feature Engineering
# -----------------------------------------------
# 📌 Add Transaction Hour
df["Transaction_Hour"] = pd.to_datetime(df["Transaction_Time"], format="%H:%M:%S").dt.hour

# 📌 Create Transaction Frequency per User
df["Transaction_Count"] = df.groupby("Customer_ID")["Customer_ID"].transform("count")

# 📌 Create a Rolling Average Transaction Amount per User (last 5 transactions)
df["Rolling_Avg_Amount"] = df.groupby("Customer_ID")["Transaction_Amount"].transform(lambda x: x.rolling(5, min_periods=1).mean())

# 📌 Create Relative Transaction Amount (compared to user’s average)
df["Avg_Transaction_Amount"] = df.groupby("Customer_ID")["Transaction_Amount"].transform("mean")
df["Relative_Amount"] = df["Transaction_Amount"] / (df["Avg_Transaction_Amount"] + 1e-9)  # Avoid division by zero

# 📌 Drop redundant columns (optional)
df.drop(columns=["Transaction_Date", "Transaction_Time", "Avg_Transaction_Amount"], inplace=True)

# -----------------------------------------------
# 🚀 Step 6: Save the Cleaned Dataset
# -----------------------------------------------
cleaned_file_path = "Cleaned_Bank_Fraud_Dataset.csv"
df.to_csv(cleaned_file_path, index=False)

print("\n✅ Data Preprocessing Complete! Cleaned dataset saved as:", cleaned_file_path)
print("\n🔍 Cleaned Dataset Sample:\n", df.head())

# -----------------------------------------------
# 🚀 Step 7: Visualize Processed Data (Optional)
# -----------------------------------------------
plt.figure(figsize=(6, 4))
sns.countplot(x=df["Is_Fraud"], palette=["green", "red"])
plt.title("Fraudulent vs. Non-Fraudulent Transactions")
plt.xlabel("Fraud (0 = No, 1 = Yes)")
plt.ylabel("Count")
plt.show()

plt.figure(figsize=(8, 5))
sns.histplot(df["Transaction_Amount"], bins=50, kde=True, color="blue")
plt.title("Distribution of Transaction Amounts")
plt.xlabel("Transaction Amount ($)")
plt.ylabel("Frequency")
plt.show()

plt.figure(figsize=(10, 5))
fraud_per_type = df.groupby("Transaction_Type")["Is_Fraud"].mean() * 100
fraud_per_type.sort_values(ascending=False).plot(kind="bar", color="purple")
plt.title("Percentage of Fraudulent Transactions per Transaction Type")
plt.ylabel("Fraud Percentage (%)")
plt.xticks(rotation=45)
plt.show()
