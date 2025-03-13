import styles from "../styles/navbar.module.css";
import { Link } from "react-router-dom";
import { useState, useEffect } from "react";
import authConfig from "../auth/authConfig.json";
import SearchCard from "./SearchCard";
import { useNavigate } from "react-router-dom";

const Navbar = () => {
  const [searchQuery, setSearchQuery] = useState("");
  const [accessToken, setAccessToken] = useState("");
  const [songs, setSongs] = useState([]);
  const navigate = useNavigate();

  const clientId = authConfig.clientId;
  const clientSecret = authConfig.clientSecret;
  const redirectUri = authConfig.redirectUri;

  const handleSearchChange = (e) => {
    setSearchQuery(e.target.value);
  };

  const handleSearchSubmit = (e) => {
    e.preventDefault();
    if (accessToken) {
      search();
    } else {
      console.error("Access token not available yet.");
    }
  };

  useEffect(() => {
    // Fetch API Access Token as it refreshes every hour
    const authParameters = {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: `grant_type=client_credentials&client_id=${clientId}&client_secret=${clientSecret}`,
    };

    fetch('https://accounts.spotify.com/api/token', authParameters)
      .then((result) => result.json())
      .then((data) => setAccessToken(data.access_token));
  }, []);

  // Search function for songs
  const search = async () => {
    console.log("Searching for " + searchQuery);

    const searchParameters = {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer ' + accessToken,
      },
    };

    //Limit the amount of searches to 5
    try {
      const searchRes = await fetch(`https://api.spotify.com/v1/search?q=${searchQuery}&type=track&limit=5`, searchParameters);
      const data = await searchRes.json();
      const tracks = data.tracks.items;

      if (tracks.length > 0) {
        console.log("Found songs:", tracks);
        setSongs(tracks);
      } else {
        console.log("No songs found.");
      }
    } catch (err) {
      console.error("Error fetching search results:", err);
    }
  };

  const handleSongClick = (songId) => {
    navigate(`/song/${songId}`);
  };

  return (
    <nav className={`${styles["navbar"]}`}>
      <div className="navHome">
        <Link to="/">Home</Link>
      </div>

      <div className="navSearch">
        <form onSubmit={handleSearchSubmit} className={styles.searchForm}>
          <input
            type="text"
            value={searchQuery}
            onChange={handleSearchChange}
            placeholder="Search for songs"
            className={styles.searchInput}
            onKeyDown={(event) => {
              if (event.key === "Enter") {
                if (accessToken) {
                  search();
                }
              }
            }}
          />
        </form>
      </div>

      <div className="navAccount">
        <Link to="/profile">Profile</Link>
        <Link to="/fileInputPage">Files</Link>
      </div>

      <SearchCard songs={songs} onSongClick={handleSongClick} />
    </nav>
  );
};

export default Navbar;
