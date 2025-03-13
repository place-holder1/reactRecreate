// import Navbar from "./components/Navbar"
// import Navbar from "./components/Navbar"
import './App.css'
// import { getTokenFromResponse } from "./auth/auth";
import { useContext } from "react";
import HomePage from "./pages/HomePage";
import FileInputPage from "./pages/FileInputPage";
import { HashRouter, Routes, Route } from "react-router-dom";
import ModeContext from "./contexts/ModeContext"; 
import SongDetailPage from "./pages/SongDetailPage";

<script
  crossOrigin="anonymous"
  src="//unpkg.com/react-scan/dist/auto.global.js"
/>

// const spotify = new SpotifyWebApi();

const App = () => {
  //const [count, setCount] = useState(0)
  const { mode } = useContext(ModeContext);

  return (
    // <AuthProvider>
      <HashRouter>
        <main className={mode === "light" ? "light" : "dark"}>
          <Routes>
            <Route path="/" element={<HomePage />} />
            <Route path="/song/:songId" element={<SongDetailPage />} />
            <Route path="/fileInputPage" element={<FileInputPage />} />
          </Routes>
        </main>
      </HashRouter>
    // </AuthProvider>
  )
}

export default App
