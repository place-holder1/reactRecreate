import { useState, useEffect } from "react";
import style from "../styles/fileInput.module.css";

const FileInput = () => {
  const [data, setData] = useState({ name: "", title: "", email: "", bio: "", image: null });
  const [errors, setErrors] = useState({ image: "", general: "" });
  const [submitting, setSubmitting] = useState(false);
  const [successMessage, setSuccessMessage] = useState("");
  const [storedData, setStoredData] = useState(null);

  // Load data from localStorage on component mount
  useEffect(() => {
    const stored = localStorage.getItem("profileData");
    if (stored) {
      setStoredData(JSON.parse(stored));
    }
  }, []);

  const handleChange = (e) => {
    if (e.target.name === "image") {
      const file = e.target.files[0];
      if (file.size > 2000000) {
        setErrors({ ...errors, image: "Image must be less than 2MB." });
      } else {
        setData({ ...data, image: file });
      }
    } else {
      setData({ ...data, [e.target.name]: e.target.value });
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setSubmitting(true);

    const formData = new FormData();
    formData.append("name", data.name.trim());
    formData.append("email", data.email.trim());
    formData.append("title", data.title.trim());
    formData.append("bio", data.bio.trim());
    if (data.image) formData.append("image", data.image);

    try {
      // Add elijah-gill.com's database and fix it eventually
      // const response = await fetch("https://elijah-gill.com/phpdb/send-data.php", {
      const response = await fetch("https://web.ics.purdue.edu/~zong6/profile-app/send-data-with-id.php", {
        method: "POST",
        body: formData,
      });
      const result = await response.json();

      if (result.success) {
        // Store data locally on successful submission
        localStorage.setItem("profileData", JSON.stringify(data));
        setStoredData(data);
        setData({ name: "", title: "", email: "", bio: "", image: null });
        setErrors({ image: "", general: "" });
        setSuccessMessage("Data submitted and stored successfully.");
        setTimeout(() => {
          setSuccessMessage("");
        }, 1000);
      } else {
        setErrors({ image: "", general: result.message });
        setSuccessMessage("");
      }
    } catch (error) {
      setErrors({ image: "", general: "Error submitting data." });
    } finally {
      setSubmitting(false);
    }
  };

  // Clear stored data from localStorage
  const clearStoredData = () => {
    localStorage.removeItem("profileData");
    setStoredData(null);
  };

  return (
    <div>
      <form onSubmit={handleSubmit} className={style["profile-form"]}>
        <input
          type="text"
          name="name"
          placeholder="Name"
          required
          value={data.name}
          onChange={handleChange}
        />
        <input
          type="email"
          name="email"
          placeholder="Email"
          required
          value={data.email}
          onChange={handleChange}
        />
        <input
          type="text"
          name="title"
          placeholder="Title"
          required
          value={data.title}
          onChange={handleChange}
        />
        <textarea
          name="bio"
          placeholder="Some description"
          maxLength={200}
          required
          value={data.bio}
          onChange={handleChange}
        ></textarea>
        <p>{data.bio.length}/200</p>
        <label htmlFor="image">Choose a profile picture:</label>
        <input
          type="file"
          id="image"
          name="image"
          accept="image/png, image/jpeg, image/jpg, image/gif"
          onChange={handleChange}
        />
        {errors.image && <p className={style["error"]}>{errors.image}</p>}
        <button
          type="submit"
          disabled={
            submitting ||
            errors.image !== "" ||
            data.name.trim() === "" ||
            data.email.trim() === "" ||
            data.title.trim() === "" ||
            data.bio.trim() === "" ||
            data.image === null
          }
        >
          Submit
        </button>
        {errors.general && <p className={style["error"]}>{errors.general}</p>}
        {successMessage && <p className={style["success"]}>{successMessage}</p>}
      </form>

      {storedData && (
        <div className={style["stored-data"]}>
          <h3>Stored Data:</h3>
          <p>Name: {storedData.name}</p>
          <p>Email: {storedData.email}</p>
          <p>Title: {storedData.title}</p>
          <p>Bio: {storedData.bio}</p>
          {storedData.image && (
            <img
              src={URL.createObjectURL(storedData.image)}
              alt="Profile"
              width="100"
            />
          )}
          <button onClick={clearStoredData}>Clear Stored Data</button>
        </div>
      )}
    </div>
  );
};

export default FileInput;
